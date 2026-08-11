<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuestionPaper;

class QuestionPaperApiController extends Controller
{
    // ──────────────────────────────────────────────────────────────
    // GET /api/question-papers
    // Returns all papers (paginated, 10 per page) with question count
    // ──────────────────────────────────────────────────────────────
    public function index()
    {
        $papers = QuestionPaper::withCount('questions')
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => $papers->map(fn($p) => [
                'id'               => $p->id,
                'title'            => $p->title,
                'description'      => $p->description,
                'exam_name'        => $p->exam_name,
                'exam_year'        => $p->exam_year,
                'total_questions'  => $p->total_questions,
                'total_marks'      => $p->total_marks,
                'duration_minutes' => $p->duration_minutes,
                'questions_count'  => $p->questions_count,
                'created_at'       => $p->created_at->toDateString(),
            ]),
            'meta' => [
                'current_page' => $papers->currentPage(),
                'last_page'    => $papers->lastPage(),
                'per_page'     => $papers->perPage(),
                'total'        => $papers->total(),
            ],
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // GET /api/question-papers/{id}
    // Returns a single paper with all questions grouped by subject
    // Each question includes its answer options
    // ──────────────────────────────────────────────────────────────
    public function show(QuestionPaper $questionPaper)
    {
        $questionPaper->load([
            'questions.subject',
            'questions.answers',
        ]);

        // Group questions by subject name
        $grouped = $questionPaper->questions
            ->groupBy(fn($q) => optional($q->subject)->name ?? 'Unassigned')
            ->map(fn($questions, $subjectName) => [
                'subject'          => $subjectName,
                'total_questions'  => $questions->count(),
                'total_marks'      => $questions->sum(fn($q) => $q->pivot->marks),
                'questions'        => $questions->map(fn($q) => [
                    'id'            => $q->id,
                    'order'         => $q->pivot->order,
                    'marks'         => $q->pivot->marks,
                    'question'      => $q->question,
                    'image_url'     => $q->image_url,
                    'question_type' => $q->question_type,
                    'options'       => $q->answers->map(fn($a) => [
                        'id'         => $a->id,
                        'answer'     => $a->answer,
                        'is_correct' => $a->is_correct,
                    ])->values(),
                ])->values(),
            ])->values();

        return response()->json([
            'success' => true,
            'data'    => [
                'id'               => $questionPaper->id,
                'title'            => $questionPaper->title,
                'description'      => $questionPaper->description,
                'exam_name'        => $questionPaper->exam_name,
                'exam_year'        => $questionPaper->exam_year,
                'total_questions'  => $questionPaper->total_questions,
                'total_marks'      => $questionPaper->total_marks,
                'duration_minutes' => $questionPaper->duration_minutes,
                'marking_scheme'   => [
                    'correct'     => '+4',
                    'incorrect'   => '-1',
                    'unattempted' => '0',
                ],
                'sections' => $grouped,
            ],
        ]);
    }
}
