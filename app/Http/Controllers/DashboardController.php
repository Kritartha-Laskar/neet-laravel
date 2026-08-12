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
            'subject',
            'resources' => function ($q) {
                $q->orderBy('sort_order');
            },
            'questions' => function ($q) {
                $q->with(['answers', 'subject'])->orderBy('sort_order');
            }
        ])->orderBy('sort_order')->get();

        // Group classes by subject name
        $groupedClasses = $classes->groupBy(function($c) {
            return $c->subject ? $c->subject->name : 'General / Unassigned';
        });

        // Load unassigned resources so they can be assigned to classes
        $unassignedResources = Resource::whereNull('study_class_id')->active()->latest()->get();
        $subjects            = Subject::orderBy('name')->get();
        $courses             = CourseName::orderBy('name')->get();

        return view('dashboard', compact(
            'coursesCount',
            'subjectsCount',
            'questionsCount',
            'usersCount',
            'classes',
            'groupedClasses',
            'unassignedResources',
            'subjects',
            'courses'
        ));
    }

    /**
     * Store a new class via AJAX/Form on home page.
     */
    public function storeClass(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:191',
            'subject_id'  => 'required|exists:subjects,id',
            'description' => 'nullable|string|max:500',
            'sort_order'  => 'nullable|integer',
        ]);

        StudyClass::create([
            'name'        => $request->name,
            'subject_id'  => $request->subject_id,
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
        $studyClass = StudyClass::find($request->study_class_id);

        $updateData = [
            'study_class_id' => $request->study_class_id,
            'sort_order'     => $request->sort_order ?? 0,
        ];

        if ($studyClass && $studyClass->subject_id) {
            $updateData['subject_id'] = $studyClass->subject_id;
            if ($studyClass->subject) {
                $updateData['subject'] = $studyClass->subject->name;
            }
        }

        $resource->update($updateData);

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
            'subject_id'  => 'required|exists:subjects,id',
            'description' => 'nullable|string|max:500',
            'sort_order'  => 'nullable|integer',
        ]);

        $studyClass->update([
            'name'        => $request->name,
            'subject_id'  => $request->subject_id,
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
        $tempFilePath = $request->input('temp_file_path');

        $rules = [
            'title'          => 'required|string|max:191',
            'description'    => 'nullable|string|max:1000',
            'type'           => 'required|in:video,pdf,image',
            'course_id'      => 'nullable|exists:course_names,id',
            'subject_id'     => 'nullable|exists:subjects,id',
            'study_class_id' => 'required|exists:study_classes,id',
            'sort_order'     => 'nullable|integer|min:0',
        ];

        if ($tempFilePath) {
            $rules['temp_file_path'] = 'required|string';
            if ($type === 'video') {
                $rules['thumbnail'] = 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048';
            }
        } else {
            if ($type === 'video') {
                $rules['file'] = 'required|file|mimes:mp4,avi,mov,qt,webm,mkv,wmv|max:512000'; // 500 MB
                $rules['thumbnail'] = 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048';
            } elseif ($type === 'pdf') {
                $rules['file'] = 'required|file|mimes:pdf|max:51200'; // 50 MB
            } elseif ($type === 'image') {
                $rules['file'] = 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240'; // 10 MB
            }
        }

        $request->validate($rules);

        $thumbnailPath = null;
        if ($type === 'video' && $request->hasFile('thumbnail')) {
            $thumb         = $request->file('thumbnail');
            $thumbName     = time() . '_thumb_' . \Illuminate\Support\Str::slug(pathinfo($thumb->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $thumb->getClientOriginalExtension();
            $thumbnailPath = $thumb->storeAs('resources/thumbnails', $thumbName, 'public');
        }

        $tempFileFullPath = storage_path("app/chunks/{$tempFilePath}");

        if ($tempFilePath && file_exists($tempFileFullPath)) {
            $originalName = $request->input('temp_file_name', 'uploaded_file');
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            
            // Get mime type and size
            $mimeType = mime_content_type($tempFileFullPath) ?: 'application/octet-stream';
            $fileSize = filesize($tempFileFullPath);

            $folder = "resources/{$type}s";
            $fileName = time() . '_' . \Illuminate\Support\Str::slug(pathinfo($originalName, PATHINFO_FILENAME))
                        . '.' . $extension;

            // Copy file to public disk using Storage
            $filePath = \Illuminate\Support\Facades\Storage::disk('public')->putFileAs($folder, new \Illuminate\Http\File($tempFileFullPath), $fileName);

            // Clean up the temp file
            @unlink($tempFileFullPath);
        } else {
            $uploadedFile = $request->file('file');
            $folder       = "resources/{$type}s";
            $fileName     = time() . '_' . \Illuminate\Support\Str::slug(pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME))
                            . '.' . $uploadedFile->getClientOriginalExtension();

            $filePath = $uploadedFile->storeAs($folder, $fileName, 'public');
            $originalName = $uploadedFile->getClientOriginalName();
            $mimeType = $uploadedFile->getMimeType();
            $fileSize = $uploadedFile->getSize();
        }

        $subjectId = $request->subject_id;
        if (!$subjectId && $request->filled('study_class_id')) {
            $studyClass = StudyClass::find($request->study_class_id);
            if ($studyClass) {
                $subjectId = $studyClass->subject_id;
            }
        }

        $subjectName = null;
        if ($subjectId) {
            $subjObj = Subject::find($subjectId);
            if ($subjObj) {
                $subjectName = $subjObj->name;
            }
        }

        Resource::create([
            'study_class_id' => $request->study_class_id,
            'course_id'      => $request->course_id,
            'subject_id'     => $subjectId,
            'sort_order'     => $request->sort_order ?? 0,
            'title'          => $request->title,
            'description'    => $request->description,
            'type'           => $type,
            'file_path'      => $filePath,
            'file_name'      => $originalName,
            'mime_type'      => $mimeType,
            'file_size'      => $fileSize,
            'thumbnail_path' => $thumbnailPath,
            'subject'        => $subjectName,
            'is_active'      => true,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Resource uploaded and assigned successfully.'
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Resource uploaded and assigned successfully.');
    }

    /**
     * Store file chunks sequentially.
     */
    public function uploadChunk(Request $request)
    {
        $request->validate([
            'file_uuid'    => 'required|string',
            'chunk_index'  => 'required|integer',
            'total_chunks' => 'required|integer',
            'file_name'    => 'required|string',
            'file_chunk'   => 'required|file',
        ]);

        $fileUuid = $request->input('file_uuid');
        $chunkIndex = $request->input('chunk_index');
        $totalChunks = $request->input('total_chunks');
        $fileName = $request->input('file_name');
        $chunkFile = $request->file('file_chunk');

        // Store chunk in temp directory
        $tempPath = storage_path("app/chunks/{$fileUuid}");
        if (!file_exists($tempPath)) {
            mkdir($tempPath, 0777, true);
        }

        // Store current chunk
        $chunkFile->move($tempPath, "chunk_{$chunkIndex}");

        // Check if all chunks are uploaded
        $uploadedChunks = count(glob("{$tempPath}/chunk_*"));

        if ($uploadedChunks === (int) $totalChunks) {
            // Merge all chunks
            $finalPath = storage_path("app/chunks/merged_{$fileUuid}");
            $out = fopen($finalPath, 'wb');

            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkFilePath = "{$tempPath}/chunk_{$i}";
                $in = fopen($chunkFilePath, 'rb');
                while ($buff = fread($in, 4096)) {
                    fwrite($out, $buff);
                }
                fclose($in);
                @unlink($chunkFilePath);
            }
            fclose($out);

            // Clean up chunk folder
            rmdir($tempPath);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded and merged successfully.',
                'temp_file_path' => "merged_{$fileUuid}",
                'original_name' => $fileName
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Chunk {$chunkIndex} uploaded successfully."
        ]);
    }

    /**
     * View or stream a resource securely/directly.
     */
    public function viewResource(Resource $resource)
    {
        $filePath = storage_path("app/public/{$resource->file_path}");
        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->file($filePath, [
            'Content-Type' => $resource->mime_type ?: mime_content_type($filePath) ?: 'application/octet-stream',
        ]);
    }

    /**
     * View or stream a thumbnail directly.
     */
    public function viewThumbnail(Resource $resource)
    {
        if (!$resource->thumbnail_path) {
            abort(404, 'Thumbnail not found');
        }

        $filePath = storage_path("app/public/{$resource->thumbnail_path}");
        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->file($filePath);
    }

    /**
     * View or stream a question image directly.
     */
    public function viewQuestionImage(Question $question)
    {
        if (!$question->image) {
            abort(404, 'Question image not found');
        }

        $filePath = storage_path("app/public/{$question->image}");
        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->file($filePath);
    }
}
