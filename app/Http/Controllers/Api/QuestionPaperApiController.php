<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuestionPaper;

class QuestionPaperApiController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/question-papers
    // Returns papers (paginated) filtered by paper_type ('mocktest' vs 'combined')
    // ─────────────────────────────────────────────────────────────────────────
    public function index(\Illuminate\Http\Request $request)
    {
        $query = QuestionPaper::with(['subject'])->withCount('questions')->latest();

        if ($request->filled('paper_type')) {
            $type = $request->query('paper_type');
            if ($type === 'mocktest') {
                // Mock test papers: paper_type == 'mocktest' OR subject_id is NOT null
                $query->where(function ($q) {
                    $q->where('paper_type', 'mocktest')
                      ->orWhereNotNull('subject_id');
                });
            } elseif ($type === 'combined') {
                // Combined test papers: paper_type == 'combined' AND subject_id is null
                $query->where(function ($q) {
                    $q->where('paper_type', 'combined')
                      ->whereNull('subject_id');
                });
            } else {
                $query->where('paper_type', $type);
            }
        }

        $perPage = min($request->query('per_page', 50), 100);
        $papers  = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $papers->map(fn($p) => [
                'id'               => $p->id,
                'title'            => $p->title,
                'description'      => $p->description,
                'exam_name'        => $p->exam_name,
                'paper_type'       => $p->subject_id !== null ? 'mocktest' : ($p->paper_type ?? 'combined'),
                'subject_id'       => $p->subject_id,
                'subject_name'     => optional($p->subject)->name,
                'subject_quotas'   => $p->subject_quotas,
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

    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/question-papers/{id}
    // Returns a single paper with all questions grouped by subject
    // ─────────────────────────────────────────────────────────────────────────
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
                'paper_type'       => $questionPaper->subject_id !== null ? 'mocktest' : ($questionPaper->paper_type ?? 'combined'),
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