<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\CourseName;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ResourceController extends Controller
{
    // Allowed MIME types per upload type
    private array $allowedMimes = [
        'video' => ['video/mp4', 'video/avi', 'video/quicktime', 'video/x-matroska', 'video/webm'],
        'pdf'   => ['application/pdf'],
        'image' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
    ];

    public function index()
    {
        $videos = Resource::videos()->latest()->get();
        $pdfs   = Resource::pdfs()->latest()->get();
        $images = Resource::images()->latest()->get();

        return view('corse.resource.index', compact('videos', 'pdfs', 'images'));
    }

    public function create()
    {
        $courses = CourseName::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        return view('corse.resource.create', compact('courses', 'subjects'));
    }

    public function store(Request $request)
    {
        $type = $request->input('type');

        // Dynamic validation based on type
        $rules = [
            'title'       => 'required|string|max:191',
            'description' => 'nullable|string|max:1000',
            'type'        => 'required|in:video,pdf,image',
            'sort_order'  => 'nullable|integer|min:1',
            'course_id'   => 'nullable|exists:course_names,id',
            'subject_id'  => 'nullable|exists:subjects,id',
        ];

        if ($type === 'video') {
            $rules['file'] = 'required|file|mimetypes:video/mp4,video/avi,video/quicktime,video/webm|max:512000'; // 500 MB
            $rules['thumbnail'] = 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048';
        } elseif ($type === 'pdf') {
            $rules['file'] = 'required|file|mimes:pdf|max:51200'; // 50 MB
        } elseif ($type === 'image') {
            $rules['file'] = 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240'; // 10 MB
        }

        $request->validate($rules);

        $uploadedFile = $request->file('file');
        $folder       = "resources/{$type}s"; // e.g. resources/videos
        $fileName     = time() . '_' . Str::slug(pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME))
                        . '.' . $uploadedFile->getClientOriginalExtension();

        $filePath = $uploadedFile->storeAs($folder, $fileName, 'public');

        // Handle video thumbnail
        $thumbnailPath = null;
        if ($type === 'video' && $request->hasFile('thumbnail')) {
            $thumb         = $request->file('thumbnail');
            $thumbName     = time() . '_thumb_' . $fileName . '.' . $thumb->getClientOriginalExtension();
            $thumbnailPath = $thumb->storeAs('resources/thumbnails', $thumbName, 'public');
        }

        $subjectName = null;
        if ($request->filled('subject_id')) {
            $subjObj = Subject::find($request->subject_id);
            if ($subjObj) {
                $subjectName = $subjObj->name;
            }
        }

        $nextSortOrder = $request->filled('sort_order')
            ? (int) $request->sort_order
            : ((Resource::max('sort_order') ?? 0) + 1);

        Resource::create([
            'title'          => $request->title,
            'description'    => $request->description,
            'type'           => $type,
            'file_path'      => $filePath,
            'file_name'      => $uploadedFile->getClientOriginalName(),
            'mime_type'      => $uploadedFile->getMimeType(),
            'file_size'      => $uploadedFile->getSize(),
            'thumbnail_path' => $thumbnailPath,
            'course_id'      => $request->course_id,
            'subject_id'     => $request->subject_id,
            'subject'        => $subjectName,
            'sort_order'     => $nextSortOrder,
            'is_active'      => true,
        ]);

        return redirect()->route('admin.resources.index')
                         ->with('success', ucfirst($type) . ' uploaded successfully.');
    }

    public function destroy(Resource $resource)
    {
        // Delete file from storage
        Storage::disk('public')->delete($resource->file_path);
        if ($resource->thumbnail_path) {
            Storage::disk('public')->delete($resource->thumbnail_path);
        }

        $resource->delete();

        return redirect()->route('admin.resources.index')
                         ->with('success', 'Resource deleted successfully.');
    }

    /**
     * Toggle active/inactive status
     */
    public function toggleStatus(Resource $resource)
    {
        $resource->update(['is_active' => !$resource->is_active]);

        return redirect()->route('admin.resources.index')
                         ->with('success', 'Resource status updated.');
    }
}
