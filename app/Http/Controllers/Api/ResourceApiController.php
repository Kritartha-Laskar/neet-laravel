<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use Illuminate\Http\Request;

class ResourceApiController extends Controller
{
    /**
     * GET /api/resources?type=video|pdf|image&page=1
     * Returns a paginated list of active resources, optionally filtered by type.
     */
    public function index(Request $request)
    {
        $query = Resource::active()->orderBy('sort_order', 'asc')->orderBy('id', 'desc');

        if ($request->filled('type') && in_array($request->type, ['video', 'pdf', 'image'])) {
            $query->where('type', $request->type);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $resources = $query->paginate(12);

        return response()->json([
            'success' => true,
            'data'    => $resources->map(fn($r) => $this->formatResource($r)),
            'meta'    => [
                'current_page' => $resources->currentPage(),
                'last_page'    => $resources->lastPage(),
                'per_page'     => $resources->perPage(),
                'total'        => $resources->total(),
            ],
        ]);
    }

    /**
     * GET /api/resources/videos   — list only videos
     * GET /api/resources/pdfs     — list only PDFs
     * GET /api/resources/images   — list only images
     */
    public function byType(string $type)
    {
        $typeMap = ['videos' => 'video', 'pdfs' => 'pdf', 'images' => 'image'];

        if (! array_key_exists($type, $typeMap)) {
            return response()->json(['success' => false, 'message' => 'Invalid resource type.'], 404);
        }

        $resources = Resource::active()
            ->where('type', $typeMap[$type])
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(12);

        return response()->json([
            'success' => true,
            'type'    => $typeMap[$type],
            'data'    => $resources->map(fn($r) => $this->formatResource($r)),
            'meta'    => [
                'current_page' => $resources->currentPage(),
                'last_page'    => $resources->lastPage(),
                'per_page'     => $resources->perPage(),
                'total'        => $resources->total(),
            ],
        ]);
    }

    /**
     * GET /api/resources/{id}   — single resource detail
     */
    public function show(Resource $resource)
    {
        if (! $resource->is_active) {
            return response()->json(['success' => false, 'message' => 'Resource not available.'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->formatResource($resource, detailed: true),
        ]);
    }

    // ── Helper ───────────────────────────────────────────────────────

    private function formatResource(Resource $r, bool $detailed = false): array
    {
        $data = [
            'id'             => $r->id,
            'title'          => $r->title,
            'description'    => $r->description,
            'type'           => $r->type,
            'serial_no'      => $r->sort_order ?? $r->id,
            'sort_order'     => $r->sort_order ?? $r->id,
            'course_id'      => $r->course_id,
            'subject_id'     => $r->subject_id,
            'subject'        => $r->subject,
            'file_url'       => $r->file_url,
            'file_name'      => $r->file_name,
            'file_size'      => $r->file_size,
            'file_size_human'=> $r->file_size_human,
            'mime_type'      => $r->mime_type,
            'thumbnail_url'  => $r->thumbnail_url,
            'created_at'     => $r->created_at->toDateString(),
        ];

        return $data;
    }
}
