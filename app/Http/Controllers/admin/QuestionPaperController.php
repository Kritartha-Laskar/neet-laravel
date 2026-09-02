<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionPaper;
use App\Models\Subject;
use App\Models\CourseName;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionPaperController extends Controller
{
    /**
     * List all question papers with filters, course, subject, and chapter options.
     */
    public function index(Request $request)
    {
        $query = QuestionPaper::with(['course', 'subject', 'chapter'])->withCount('questions')->latest();

        if ($request->filled('paper_type')) {
            $query->where('paper_type', $request->query('paper_type'));
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->query('subject_id'));
        }

        if ($request->filled('chapter_id')) {
            $query->where('chapter_id', $request->query('chapter_id'));
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->query('course_id'));
        }

        $papers   = $query->paginate(30);
        $courses  = CourseName::orderBy('name')->get();
        $subjects = Subject::withCount([
            'questions',
            'questionPapers' => function($q) {
                $q->where('paper_type', 'mocktest');
            }
        ])->with(['chapters' => function($q) {
            $q->withCount(['questionPapers' => function($qp) {
                $qp->where('paper_type', 'mocktest');
            }]);
        }])->orderBy('name')->get();

        return view('corse.question_paper.index', compact('papers', 'courses', 'subjects'));
    }

    /**
     * Show a single question paper with all questions grouped by subject and unattached available questions.
     */
    /**
     * Show a single question paper with all questions grouped by subject and unattached available questions.
     */
    public function show(QuestionPaper $questionPaper)
    {
        $questionPaper->load(['course', 'subject', 'chapter', 'questions.subject', 'questions.answers']);

        $attachedIds = $questionPaper->questions->pluck('id');

        $query = Question::whereNotIn('id', $attachedIds)->whereNull('deleted_at')->with('subject');
        
        if ($questionPaper->course_id) {
            $query->whereHas('subject', function($q) use ($questionPaper) {
                $q->where('course_id', $questionPaper->course_id);
            });
        }

        if ($questionPaper->isMockTest() && $questionPaper->subject_id) {
            $query->where('subject_id', $questionPaper->subject_id);
        }

        $availableQuestions = $query->latest()->get();

        $grouped = $questionPaper->questions
            ->groupBy(fn($q) => optional($q->subject)->name ?? 'Unassigned');

        $subjects = Subject::orderBy('name')->get();

        return view('corse.question_paper.show', compact('questionPaper', 'grouped', 'availableQuestions', 'subjects'));
    }

    /**
     * Store/Create a new question paper (Mock Test or Combined).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'paper_type'        => 'required|in:mocktest,combined',
            'title'             => 'required|string|max:191|unique:question_paper,title',
            'course_id'         => 'nullable|exists:course_names,id',
            'exam_name'         => 'nullable|string|max:100',
            'exam_year'         => 'nullable|integer|min:2000|max:2100',
            'duration_minutes'  => 'nullable|integer|min:1|max:600',
            'total_marks'       => 'nullable|integer|min:1|max:2000',
            'description'       => 'nullable|string|max:500',
            'subject_id'        => 'required_if:paper_type,mocktest|nullable|exists:subjects,id',
            'chapter_id'        => 'nullable|exists:chapters,id',
        ]);

        $paperType = $validated['paper_type'];
        $courseId  = $validated['course_id'] ?? null;
        $chapterId = $validated['chapter_id'] ?? null;

        if ($paperType === 'mocktest') {
            $subjectId = $validated['subject_id'];
            $subject   = Subject::findOrFail($subjectId);

            if (!$courseId && $subject->course_id) {
                $courseId = $subject->course_id;
            }

            $paper = QuestionPaper::create([
                'title'            => $validated['title'],
                'description'      => $validated['description'] ?? ("Mock test paper for " . $subject->name),
                'exam_name'        => $validated['exam_name'] ?? 'NEET Subject Mock',
                'course_id'        => $courseId,
                'paper_type'       => 'mocktest',
                'subject_id'       => $subjectId,
                'chapter_id'       => $chapterId,
                'subject_quotas'   => [$subject->name => 0],
                'exam_year'        => $validated['exam_year'] ?? date('Y'),
                'duration_minutes' => $validated['duration_minutes'] ?? 180,
                'total_marks'      => $validated['total_marks'] ?? 720,
                'total_questions'  => 0,
            ]);

        } else {
            $paper = QuestionPaper::create([
                'title'            => $validated['title'],
                'description'      => $validated['description'] ?? 'Combined multi-subject question paper.',
                'exam_name'        => $validated['exam_name'] ?? 'NEET Combined Test',
                'course_id'        => $courseId,
                'paper_type'       => 'combined',
                'subject_id'       => null,
                'chapter_id'       => null,
                'subject_quotas'   => [],
                'exam_year'        => $validated['exam_year'] ?? date('Y'),
                'duration_minutes' => $validated['duration_minutes'] ?? 180,
                'total_marks'      => $validated['total_marks'] ?? 720,
                'total_questions'  => 0,
            ]);
        }

        return redirect()->route('admin.question-papers.show', $paper->id)
            ->with('success', 'Question Paper created successfully! You can now type new questions or select existing questions to add to this paper.');
    }

    /**
     * Add selected questions to an existing question paper.
     */
    public function addQuestions(Request $request, QuestionPaper $questionPaper)
    {
        $request->validate([
            'question_ids'   => 'required|array|min:1',
            'question_ids.*' => 'exists:questions,id',
        ]);

        $currentMaxOrder = DB::table('question_paper_question')
            ->where('question_paper_id', $questionPaper->id)
            ->max('order') ?? 0;

        $attachedIds = $questionPaper->questions()->pluck('question_id')->toArray();
        $newQuestionIds = array_diff($request->question_ids, $attachedIds);

        $pivotRows = [];
        $order = $currentMaxOrder + 1;

        foreach ($newQuestionIds as $qid) {
            $pivotRows[] = [
                'question_paper_id' => $questionPaper->id,
                'question_id'       => $qid,
                'order'             => $order++,
                'marks'             => 4,
                'created_at'        => now(),
                'updated_at'        => now(),
            ];
        }

        if (!empty($pivotRows)) {
            DB::table('question_paper_question')->insert($pivotRows);
        }

        $totalCount = $questionPaper->questions()->count();
        $questionPaper->update([
            'total_questions' => $totalCount,
            'total_marks'     => $totalCount * 4,
        ]);

        return back()->with('success', count($pivotRows) . ' question(s) added to this paper successfully.');
    }

    /**
     * Remove a question from a question paper.
     */
    public function removeQuestion(QuestionPaper $questionPaper, Question $question)
    {
        $questionPaper->questions()->detach($question->id);

        $totalCount = $questionPaper->questions()->count();
        $questionPaper->update([
            'total_questions' => $totalCount,
            'total_marks'     => $totalCount * 4,
        ]);

        return back()->with('success', 'Question removed from paper successfully.');
    }

    /**
     * Delete a question paper.
     */
    public function destroy(QuestionPaper $questionPaper)
    {
        $questionPaper->questions()->detach();
        $questionPaper->delete();

        return redirect()->route('admin.question-papers.index')
            ->with('success', 'Question Paper deleted successfully.');
    }
}
