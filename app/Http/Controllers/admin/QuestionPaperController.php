<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionPaper;
use App\Models\Subject;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionPaperController extends Controller
{
    /**
     * List all question papers.
     */
    public function index()
    {
        $papers = QuestionPaper::withCount('questions')->latest()->paginate(15);
        return view('corse.question_paper.index', compact('papers'));
    }

    /**
     * Show a single question paper with all questions grouped by subject.
     */
    public function show(QuestionPaper $questionPaper)
    {
        // Eager-load questions → subject → answers in one shot
        $questionPaper->load(['questions.subject', 'questions.answers']);

        $grouped = $questionPaper->questions
            ->groupBy(fn($q) => optional($q->subject)->name ?? 'Unassigned');

        return view('corse.question_paper.show', compact('questionPaper', 'grouped'));
    }

    /**
     * Automatically generate a NEET mock question paper based on Biology (90), Physics (45), Chemistry (45).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:191|unique:question_paper,title',
            'exam_name'        => 'nullable|string|max:100',
            'exam_year'        => 'nullable|integer|min:2000|max:2100',
            'duration_minutes' => 'nullable|integer|min:1|max:600',
            'total_marks'      => 'nullable|integer|min:1|max:2000',
            'description'      => 'nullable|string|max:500',
        ]);

        $subjectQuota = [
            'Biology'   => 90,
            'Physics'   => 45,
            'Chemistry' => 45,
        ];

        // Ensure we actually have questions in the database
        $totalQuestionsInDb = Question::count();
        if ($totalQuestionsInDb === 0) {
            return back()->withErrors(['error' => 'No questions found in the database. Please add questions or seed the database first.']);
        }

        $paper = QuestionPaper::create([
            'title'            => $validated['title'],
            'description'      => $validated['description'] ?? 'Automatically generated NEET mock question paper.',
            'exam_name'        => $validated['exam_name'] ?? 'NEET',
            'exam_year'        => $validated['exam_year'] ?? date('Y'),
            'duration_minutes' => $validated['duration_minutes'] ?? 180,
            'total_marks'      => $validated['total_marks'] ?? 720,
            'total_questions'  => 180,
        ]);

        $pivotRows = [];
        $order = 1;

        foreach ($subjectQuota as $subjectName => $quota) {
            // Find subject case-insensitively
            $subject = Subject::where('name', $subjectName)
                ->orWhere('name', 'like', '%' . $subjectName . '%')
                ->first();

            if (!$subject) {
                continue;
            }

            $questionIds = Question::where('subject_id', $subject->id)
                ->whereNull('deleted_at')
                ->inRandomOrder()
                ->limit($quota)
                ->pluck('id');

            foreach ($questionIds as $qid) {
                $pivotRows[] = [
                    'question_paper_id' => $paper->id,
                    'question_id'       => $qid,
                    'order'             => $order++,
                    'marks'             => 4,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ];
            }
        }

        // If no questions were added (e.g. subjects not matched), grab random questions as fallback
        if (empty($pivotRows)) {
            $questionIds = Question::whereNull('deleted_at')
                ->inRandomOrder()
                ->limit(180)
                ->pluck('id');

            foreach ($questionIds as $qid) {
                $pivotRows[] = [
                    'question_paper_id' => $paper->id,
                    'question_id'       => $qid,
                    'order'             => $order++,
                    'marks'             => 4,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ];
            }
        }

        if (!empty($pivotRows)) {
            DB::table('question_paper_question')->insert($pivotRows);
        }

        // Update the paper's actual attached question count
        $paper->update([
            'total_questions' => count($pivotRows)
        ]);

        return redirect()->route('admin.question-papers.show', $paper->id)
            ->with('success', 'Question Paper generated successfully with ' . count($pivotRows) . ' questions.');
    }
}
