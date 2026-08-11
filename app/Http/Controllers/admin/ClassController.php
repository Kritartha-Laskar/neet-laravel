<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Resource;
use App\Models\StudyClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClassController extends Controller
{
    /**
     * Display classes folder view and class materials/MCQ playlist.
     */
    public function index(Request $request)
    {
        $classes = StudyClass::withCount(['resources', 'questions'])
            ->with(['resources' => function ($q) {
                $q->orderBy('sort_order');
            }, 'questions' => function ($q) {
                $q->with(['subject', 'answers'])->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        $selectedClassId = $request->query('class_id', $classes->first()?->id);
        $selectedClass   = $classes->firstWhere('id', $selectedClassId);

        $unassignedResources = Resource::whereNull('study_class_id')->active()->latest()->get();
        $subjects            = Subject::orderBy('name')->get();

        return view('corse.class.class', compact(
            'classes',
            'selectedClass',
            'unassignedResources',
            'subjects'
        ));
    }

    /**
     * Create a new class folder.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:191',
            'description' => 'nullable|string|max:500',
            'sort_order'  => 'nullable|integer',
        ]);

        $class = StudyClass::create([
            'name'        => $request->name,
            'description' => $request->description,
            'sort_order'  => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.classes.index', ['class_id' => $class->id])
            ->with('success', 'Class folder created successfully.');
    }

    /**
     * Update class folder details.
     */
    public function update(Request $request, StudyClass $class)
    {
        $request->validate([
            'name'        => 'required|string|max:191',
            'description' => 'nullable|string|max:500',
            'sort_order'  => 'nullable|integer',
        ]);

        $class->update([
            'name'        => $request->name,
            'description' => $request->description,
            'sort_order'  => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.classes.index', ['class_id' => $class->id])
            ->with('success', 'Class details updated successfully.');
    }

    /**
     * Delete class folder.
     */
    public function destroy(StudyClass $class)
    {
        $class->resources()->update(['study_class_id' => null, 'sort_order' => 0]);
        $class->questions()->update(['study_class_id' => null, 'sort_order' => 0]);
        $class->delete();

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class folder deleted successfully.');
    }

    /**
     * Save serialization order for materials and questions.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'resource_orders'   => 'nullable|array',
            'resource_orders.*' => 'integer',
            'question_orders'   => 'nullable|array',
            'question_orders.*' => 'integer',
        ]);

        if ($request->has('resource_orders')) {
            foreach ($request->resource_orders as $id => $orderVal) {
                Resource::where('id', $id)->update(['sort_order' => (int)$orderVal]);
            }
        }

        if ($request->has('question_orders')) {
            foreach ($request->question_orders as $id => $orderVal) {
                Question::where('id', $id)->update(['sort_order' => (int)$orderVal]);
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Serialization order updated successfully.']);
        }

        return back()->with('success', 'Serialization order updated successfully.');
    }

    /**
     * Create MCQ question for a class.
     */
    public function storeQuestion(Request $request)
    {
        $request->validate([
            'study_class_id' => 'required|exists:study_classes,id',
            'subject_id'     => 'nullable|exists:subjects,id',
            'question'       => 'required|string',
            'sort_order'     => 'nullable|integer',
            'answers'        => 'required|array|min:2',
            'answers.*'      => 'required|string|max:500',
            'correct_index'  => 'required|integer',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $uploadedFile = $request->file('image');
            $folder       = "questions";
            $fileName     = time() . '_' . Str::slug(pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME))
                            . '.' . $uploadedFile->getClientOriginalExtension();
            $imagePath    = $uploadedFile->storeAs($folder, $fileName, 'public');
        }

        $question = Question::create([
            'study_class_id' => $request->study_class_id,
            'subject_id'     => $request->subject_id,
            'question'       => $request->question,
            'image'          => $imagePath,
            'question_type'  => 'mcq',
            'sort_order'     => $request->sort_order ?? 1,
        ]);

        $answers      = $request->input('answers', []);
        $correctIndex = (int) $request->input('correct_index', 0);

        foreach ($answers as $idx => $ansText) {
            if (trim($ansText) === '') continue;
            Answer::create([
                'question_id' => $question->id,
                'answer'      => trim($ansText),
                'is_correct'  => ($idx === $correctIndex),
            ]);
        }

        if (str_contains(url()->previous(), 'dashboard')) {
            return redirect()->route('dashboard')->with('success', 'MCQ Question created and added to class playlist.');
        }

        return redirect()->route('admin.classes.index', ['class_id' => $request->study_class_id])
            ->with('success', 'MCQ Question created and added to class playlist.');
    }

    /**
     * Delete MCQ Question from class.
     */
    public function destroyQuestion(Question $question)
    {
        $classId = $question->study_class_id;
        $question->delete();

        if (str_contains(url()->previous(), 'dashboard')) {
            return redirect()->route('dashboard')->with('success', 'MCQ Question removed.');
        }

        return redirect()->route('admin.classes.index', ['class_id' => $classId])
            ->with('success', 'MCQ Question removed.');
    }

    /**
     * Assign an existing resource to class.
     */
    public function assignResource(Request $request)
    {
        $request->validate([
            'resource_id'    => 'required|exists:resources,id',
            'study_class_id' => 'required|exists:study_classes,id',
            'sort_order'     => 'nullable|integer',
        ]);

        Resource::where('id', $request->resource_id)->update([
            'study_class_id' => $request->study_class_id,
            'sort_order'     => $request->sort_order ?? 1,
        ]);

        return redirect()->route('admin.classes.index', ['class_id' => $request->study_class_id])
            ->with('success', 'Study material assigned to class.');
    }

    /**
     * Upload and assign new resource to class.
     */
    public function uploadResource(Request $request)
    {
        $type = $request->input('type');

        $rules = [
            'title'          => 'required|string|max:191',
            'type'           => 'required|in:video,pdf,image',
            'subject'        => 'nullable|string|max:191',
            'study_class_id' => 'required|exists:study_classes,id',
            'sort_order'     => 'nullable|integer',
        ];

        if ($type === 'video') {
            $rules['file'] = 'required|file|mimes:mp4,avi,mov,qt,webm,mkv|max:512000';
            $rules['thumbnail'] = 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048';
        } elseif ($type === 'pdf') {
            $rules['file'] = 'required|file|mimes:pdf|max:51200';
        } else {
            $rules['file'] = 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240';
        }

        $request->validate($rules);

        $uploadedFile = $request->file('file');
        $folder       = "resources/{$type}s";
        $fileName     = time() . '_' . Str::slug(pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME))
                        . '.' . $uploadedFile->getClientOriginalExtension();

        $filePath = $uploadedFile->storeAs($folder, $fileName, 'public');

        $thumbnailPath = null;
        if ($type === 'video' && $request->hasFile('thumbnail')) {
            $thumb         = $request->file('thumbnail');
            $thumbName     = time() . '_thumb_' . $fileName . '.' . $thumb->getClientOriginalExtension();
            $thumbnailPath = $thumb->storeAs('resources/thumbnails', $thumbName, 'public');
        }

        Resource::create([
            'study_class_id' => $request->study_class_id,
            'sort_order'     => $request->sort_order ?? 1,
            'title'          => $request->title,
            'description'    => $request->description,
            'type'           => $type,
            'file_path'      => $filePath,
            'file_name'      => $uploadedFile->getClientOriginalName(),
            'mime_type'      => $uploadedFile->getMimeType(),
            'file_size'      => $uploadedFile->getSize(),
            'thumbnail_path' => $thumbnailPath,
            'subject'        => $request->subject,
            'is_active'      => true,
        ]);

        return redirect()->route('admin.classes.index', ['class_id' => $request->study_class_id])
            ->with('success', 'Study material uploaded and assigned to class.');
    }

    /**
     * Remove resource from class.
     */
    public function removeResource(Resource $resource)
    {
        $classId = $resource->study_class_id;
        $resource->update(['study_class_id' => null, 'sort_order' => 0]);

        return redirect()->route('admin.classes.index', ['class_id' => $classId])
            ->with('success', 'Material unassigned from class.');
    }
}
