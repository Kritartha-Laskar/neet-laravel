<?php

namespace App\Http\Controllers;

use App\Models\CourseName;
use App\Models\Subject;
use App\Models\Question;
use App\Models\User;
use App\Models\StudyClass;
use App\Models\Resource;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the dashboard home page.
     */
    public function index()
    {
        $coursesCount   = CourseName::count();
        $subjectsCount  = Subject::count();
        $questionsCount = Question::count();
        $usersCount     = User::count();

        // Load all classes with resources and questions ordered by sort_order
        $classes = StudyClass::with([
            'resources' => function ($q) {
                $q->orderBy('sort_order');
            },
            'questions' => function ($q) {
                $q->with(['answers', 'subject'])->orderBy('sort_order');
            }
        ])->orderBy('sort_order')->get();

        // Load unassigned resources so they can be assigned to classes
        $unassignedResources = Resource::whereNull('study_class_id')->active()->latest()->get();
        $subjects            = Subject::orderBy('name')->get();

        return view('dashboard', compact(
            'coursesCount',
            'subjectsCount',
            'questionsCount',
            'usersCount',
            'classes',
            'unassignedResources',
            'subjects'
        ));
    }

    /**
     * Store a new class via AJAX/Form on home page.
     */
    public function storeClass(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:191',
            'description' => 'nullable|string|max:500',
            'sort_order'  => 'nullable|integer',
        ]);

        StudyClass::create([
            'name'        => $request->name,
            'description' => $request->description,
            'sort_order'  => $request->sort_order ?? 0,
        ]);

        return redirect()->route('dashboard')->with('success', 'Class created successfully.');
    }

    /**
     * Delete a class.
     */
    public function destroyClass(StudyClass $studyClass)
    {
        // Unassign all resources inside this class
        $studyClass->resources()->update(['study_class_id' => null, 'sort_order' => 0]);
        $studyClass->delete();

        return redirect()->route('dashboard')->with('success', 'Class deleted successfully.');
    }

    /**
     * Assign resource to class and set its order/serial.
     */
    public function assignResource(Request $request)
    {
        $request->validate([
            'resource_id'    => 'required|exists:resources,id',
            'study_class_id' => 'required|exists:study_classes,id',
            'sort_order'     => 'nullable|integer|min:0',
        ]);

        $resource = Resource::find($request->resource_id);
        $resource->update([
            'study_class_id' => $request->study_class_id,
            'sort_order'     => $request->sort_order ?? 0,
        ]);

        return redirect()->route('dashboard')->with('success', 'Resource assigned to class successfully.');
    }

    /**
     * Remove resource from a class.
     */
    public function removeResource(Resource $resource)
    {
        $resource->update([
            'study_class_id' => null,
            'sort_order'     => 0,
        ]);

        return redirect()->route('dashboard')->with('success', 'Resource removed from class.');
    }

    /**
     * Update resources sorting order.
     */
    public function reorderResources(Request $request)
    {
        $request->validate([
            'orders'   => 'required|array',
            'orders.*' => 'required|integer', // key = resource ID, value = sort_order
        ]);

        foreach ($request->orders as $id => $orderVal) {
            Resource::where('id', $id)->update(['sort_order' => $orderVal]);
        }

        return response()->json(['success' => true, 'message' => 'Sorting order updated successfully.']);
    }

    /**
     * Update an existing class.
     */
    public function updateClass(Request $request, StudyClass $studyClass)
    {
        $request->validate([
            'name'        => 'required|string|max:191',
            'description' => 'nullable|string|max:500',
            'sort_order'  => 'nullable|integer',
        ]);

        $studyClass->update([
            'name'        => $request->name,
            'description' => $request->description,
            'sort_order'  => $request->sort_order ?? 0,
        ]);

        return redirect()->route('dashboard')->with('success', 'Class updated successfully.');
    }

    /**
     * Upload and assign a resource to a class.
     */
    public function uploadAndAssignResource(Request $request)
    {
        $type = $request->input('type');

        $rules = [
            'title'          => 'required|string|max:191',
            'description'    => 'nullable|string|max:1000',
            'type'           => 'required|in:video,pdf,image',
            'subject'        => 'nullable|string|max:191',
            'study_class_id' => 'required|exists:study_classes,id',
            'sort_order'     => 'nullable|integer|min:0',
        ];

        if ($type === 'video') {
            $rules['file'] = 'required|file|mimes:mp4,avi,mov,qt,webm,mkv,wmv|max:512000'; // 500 MB
            $rules['thumbnail'] = 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048';
        } elseif ($type === 'pdf') {
            $rules['file'] = 'required|file|mimes:pdf|max:51200'; // 50 MB
        } elseif ($type === 'image') {
            $rules['file'] = 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240'; // 10 MB
        }

        $request->validate($rules);

        $uploadedFile = $request->file('file');
        $folder       = "resources/{$type}s";
        $fileName     = time() . '_' . \Illuminate\Support\Str::slug(pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME))
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
            'sort_order'     => $request->sort_order ?? 0,
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

        return redirect()->route('dashboard')->with('success', 'Resource uploaded and assigned successfully.');
    }
}
