<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\QuestionPaper;
use App\Models\UserExamAnswer;
use App\Models\UserExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamApiController extends Controller
{
    /**
     * Start a new exam attempt session or resume active attempt.
     */
    public function startExam(Request $request)
    {
        $request->validate([
            'question_paper_id' => 'required|exists:question_paper,id',
        ]);

        $user = $request->user();
        $paper = QuestionPaper::with('questions.answers')->findOrFail($request->question_paper_id);

        // Find existing in_progress attempt or create new one
        $attempt = UserExamAttempt::firstOrCreate(
            [
                'user_id'           => $user->id,
                'question_paper_id' => $paper->id,
                'status'            => 'in_progress',
            ],
            [
                'total_questions'   => $paper->questions->count(),
                'total_marks'       => $paper->total_marks ?? ($paper->questions->count() * 4),
                'started_at'        => now(),
            ]
        );

        // Fetch already saved answers for this attempt (if resuming)
        $savedAnswers = UserExamAnswer::where('user_exam_attempt_id', $attempt->id)
            ->pluck('selected_answer_id', 'question_id');

        return response()->json([
            'success' => true,
            'data'    => [
                'attempt_id'       => $attempt->id,
                'status'           => $attempt->status,
                'total_questions'  => $attempt->total_questions,
                'paper_title'      => $paper->title,
                'paper_type'       => $paper->paper_type ?? 'combined',
                'duration_minutes' => $paper->duration_minutes,
                'saved_answers'    => $savedAnswers,
            ]
        ]);
    }

    /**
     * Instantly save or update a user's answer for a single question.
     */
    public function saveAnswer(Request $request)
    {
        $request->validate([
            'attempt_id'         => 'required|exists:user_exam_attempts,id',
            'question_id'        => 'required|exists:questions,id',
            'selected_answer_id' => 'nullable|exists:answers,id',
        ]);

        $user = $request->user();
        $attempt = UserExamAttempt::where('id', $request->attempt_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($attempt->status !== 'in_progress') {
            return response()->json([
                'success' => false,
                'message' => 'This exam attempt has already been completed.'
            ], 422);
        }

        $selectedAnswerId = $request->selected_answer_id;
        $isCorrect = null;

        if ($selectedAnswerId) {
            $isCorrect = Answer::where('id', $selectedAnswerId)
                ->where('question_id', $request->question_id)
                ->value('is_correct') ? true : false;

            UserExamAnswer::updateOrCreate(
                [
                    'user_exam_attempt_id' => $attempt->id,
                    'question_id'          => $request->question_id,
                ],
                [
                    'selected_answer_id'   => $selectedAnswerId,
                    'is_correct'           => $isCorrect,
                ]
            );
        } else {
            // User cleared their answer choice
            UserExamAnswer::where('user_exam_attempt_id', $attempt->id)
                ->where('question_id', $request->question_id)
                ->delete();
        }

        // Update total answered count
        $answeredCount = UserExamAnswer::where('user_exam_attempt_id', $attempt->id)
            ->whereNotNull('selected_answer_id')
            ->count();

        $attempt->update(['total_answered' => $answeredCount]);

        return response()->json([
            'success' => true,
            'message' => 'Answer saved successfully.',
            'data'    => [
                'total_answered' => $answeredCount,
            ]
        ]);
    }

    /**
     * Submit and finalize an exam attempt session.
     */
    public function submitExam(Request $request)
    {
        $request->validate([
            'attempt_id' => 'required|exists:user_exam_attempts,id',
        ]);

        $user = $request->user();
        $attempt = UserExamAttempt::where('id', $request->attempt_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($attempt->status === 'completed') {
            return response()->json([
                'success' => true,
                'message' => 'Exam already submitted.',
                'data'    => $attempt
            ]);
        }

        $paper = QuestionPaper::with('questions.answers')->findOrFail($attempt->question_paper_id);
        $userAnswers = UserExamAnswer::where('user_exam_attempt_id', $attempt->id)->get()->keyBy('question_id');

        $correctCount = 0;
        $wrongCount = 0;
        $score = 0;

        foreach ($paper->questions as $q) {
            $userAns = $userAnswers->get($q->id);
            if ($userAns && $userAns->selected_answer_id) {
                if ($userAns->is_correct) {
                    $correctCount++;
                    $score += 4; // Standard 4 marks per correct answer
                } else {
                    $wrongCount++;
                    // -1 negative marking if applicable
                    $score -= 1;
                }
            }
        }

        $score = max(0, $score); // Ensure non-negative score
        $totalMarks = max(1, $attempt->total_marks > 0 ? $attempt->total_marks : ($paper->questions->count() * 4));
        $percentage = round(($score / $totalMarks) * 100, 2);

        $attempt->update([
            'status'          => 'completed',
            'total_answered'  => $userAnswers->whereNotNull('selected_answer_id')->count(),
            'correct_answers' => $correctCount,
            'wrong_answers'   => $wrongCount,
            'score'           => $score,
            'percentage'      => $percentage,
            'completed_at'    => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Exam submitted successfully.',
            'data'    => [
                'attempt_id'       => $attempt->id,
                'paper_title'      => $paper->title,
                'paper_type'       => $paper->paper_type ?? 'combined',
                'total_questions'  => $attempt->total_questions,
                'total_answered'   => $attempt->total_answered,
                'correct_answers'  => $attempt->correct_answers,
                'wrong_answers'    => $attempt->wrong_answers,
                'total_marks'      => $attempt->total_marks,
                'score'            => $attempt->score,
                'percentage'       => $attempt->percentage,
                'completed_at'     => $attempt->completed_at->toIso8601String(),
            ]
        ]);
    }

    /**
     * Get performance analytics ratios:
     * 1. Mock Test Ratio (%)
     * 2. Combined Paper Ratio (%)
     * 3. Overall Combined Ratio (%)
     */
    public function getPerformanceAnalytics(Request $request)
    {
        $user = $request->user();

        $completedAttempts = UserExamAttempt::with('questionPaper')
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->orderBy('completed_at', 'asc')
            ->get();

        $mocktestAttempts = $completedAttempts->filter(fn($a) => optional($a->questionPaper)->isMockTest());
        $combinedAttempts = $completedAttempts->filter(fn($a) => !optional($a->questionPaper)->isMockTest());

        $mockMetrics     = $this->calculateMetrics($mocktestAttempts);
        $combinedMetrics = $this->calculateMetrics($combinedAttempts);
        $overallMetrics  = $this->calculateMetrics($completedAttempts);

        $recentHistory = $completedAttempts->reverse()->take(10)->values()->map(function ($attempt) {
            return [
                'attempt_id'      => $attempt->id,
                'paper_title'     => optional($attempt->questionPaper)->title ?? 'Question Paper',
                'paper_type'      => optional($attempt->questionPaper)->paper_type ?? 'combined',
                'total_questions' => $attempt->total_questions,
                'correct_answers' => $attempt->correct_answers,
                'wrong_answers'   => $attempt->wrong_answers,
                'score'           => $attempt->score,
                'total_marks'     => $attempt->total_marks,
                'percentage'      => $attempt->percentage,
                'completed_at'    => optional($attempt->completed_at)->format('Y-m-d H:i'),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => [
                'overall_performance'  => $overallMetrics,
                'mocktest_performance' => $mockMetrics,
                'combined_performance' => $combinedMetrics,
                'recent_history'       => $recentHistory,
            ]
        ]);
    }

    /**
     * Get detailed review of a completed attempt.
     */
    public function getAttemptDetail(Request $request, $attemptId)
    {
        $user = $request->user();
        $attempt = UserExamAttempt::with(['questionPaper.questions.answers', 'answers'])
            ->where('id', $attemptId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $userAnswers = $attempt->answers->keyBy('question_id');

        $questionsDetail = $attempt->questionPaper->questions->map(function ($q) use ($userAnswers) {
            $userAns = $userAnswers->get($q->id);
            return [
                'question_id'        => $q->id,
                'question'           => $q->question,
                'image_url'          => $q->image_url,
                'selected_answer_id' => optional($userAns)->selected_answer_id,
                'is_correct'         => optional($userAns)->is_correct,
                'answers'            => $q->answers->map(function ($a) {
                    return [
                        'id'         => $a->id,
                        'answer'     => $a->answer,
                        'is_correct' => $a->is_correct,
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => [
                'attempt_id'       => $attempt->id,
                'paper_title'      => $attempt->questionPaper->title,
                'paper_type'       => $attempt->questionPaper->paper_type ?? 'combined',
                'score'            => $attempt->score,
                'total_marks'      => $attempt->total_marks,
                'percentage'       => $attempt->percentage,
                'correct_answers'  => $attempt->correct_answers,
                'wrong_answers'    => $attempt->wrong_answers,
                'total_questions'  => $attempt->total_questions,
                'completed_at'     => optional($attempt->completed_at)->format('Y-m-d H:i'),
                'questions'        => $questionsDetail,
            ]
        ]);
    }

    /**
     * Helper to compute average %, highest %, and improvement % for a set of attempts.
     */
    private function calculateMetrics($attempts): array
    {
        $count = $attempts->count();
        if ($count === 0) {
            return [
                'total_attempts'         => 0,
                'average_percentage'     => 0.00,
                'highest_percentage'     => 0.00,
                'improvement_percentage' => '0.00%',
            ];
        }

        $avg      = round($attempts->avg('percentage'), 2);
        $highest  = round($attempts->max('percentage'), 2);
        $firstPct = $attempts->first()->percentage;
        $lastPct  = $attempts->last()->percentage;

        $diff = round($lastPct - $firstPct, 2);
        $sign = $diff >= 0 ? '+' : '';
        $improvementStr = $count >= 2 ? "{$sign}{$diff}%" : "0.00%";

        return [
            'total_attempts'         => $count,
            'average_percentage'     => $avg,
            'highest_percentage'     => $highest,
            'first_attempt_pct'      => $firstPct,
            'latest_attempt_pct'     => $lastPct,
            'improvement_percentage' => $improvementStr,
        ];
    }
}
