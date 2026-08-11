<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StudyClass;
use Illuminate\Http\Request;

class ClassApiController extends Controller
{
    /**
     * GET /api/classes
     * List all study classes ordered by class priority/sort order.
     */
    public function index()
    {
        $classes = StudyClass::withCount(['resources', 'questions'])
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $classes->map(fn($c) => [
                'id'              => $c->id,
                'name'            => $c->name,
                'description'     => $c->description,
                'sort_order'      => $c->sort_order,
                'resources_count' => $c->resources_count,
                'questions_count' => $c->questions_count,
                'created_at'      => $c->created_at->toDateString(),
            ]),
        ]);
    }

    /**
     * GET /api/classes/{studyClass}
     * Get a single study class with all its assigned study materials and MCQ questions
     * sorted by their serial sort_order.
     */
    public function show(StudyClass $studyClass)
    {
        $studyClass->load([
            'resources' => function ($q) {
                $q->active()->orderBy('sort_order');
            },
            'questions' => function ($q) {
                $q->with(['subject', 'answers'])->orderBy('sort_order');
            }
        ]);

        $materials = $studyClass->resources->map(fn($r) => [
            'id'              => $r->id,
            'item_type'       => 'resource',
            'title'           => $r->title,
            'type'            => $r->type,
            'sort_order'      => $r->sort_order,
            'subject'         => $r->subject,
            'file_url'        => $r->file_url,
            'file_name'       => $r->file_name,
            'file_size'       => $r->file_size,
            'file_size_human' => $r->file_size_human,
            'mime_type'       => $r->mime_type,
            'thumbnail_url'   => $r->thumbnail_url,
            'created_at'      => $r->created_at->toDateString(),
        ]);

        $questions = $studyClass->questions->map(fn($q) => [
            'id'           => $q->id,
            'item_type'    => 'question',
            'question'     => $q->question,
            'type'         => $q->question_type,
            'question_type'=> $q->question_type,
            'sort_order'   => $q->sort_order,
            'subject'      => optional($q->subject)->name,
            'answers'      => $q->answers->map(fn($a) => [
                'id'         => $a->id,
                'answer'     => $a->answer,
                'is_correct' => (bool)$a->is_correct,
            ]),
            'options'      => $q->answers->map(fn($a) => [
                'id'         => $a->id,
                'answer'     => $a->answer,
                'is_correct' => (bool)$a->is_correct,
            ]),
            'created_at'   => $q->created_at->toDateString(),
        ]);

        // Merge into a single playlist ordered by sort_order
        $playlist = $materials->concat($questions)->sortBy('sort_order')->values();

        return response()->json([
            'success' => true,
            'data'    => [
                'id'          => $studyClass->id,
                'name'        => $studyClass->name,
                'description' => $studyClass->description,
                'sort_order'  => $studyClass->sort_order,
                'materials'   => $materials->values(),
                'questions'   => $questions->values(),
                'playlist'    => $playlist,
            ],
        ]);
    }
}
