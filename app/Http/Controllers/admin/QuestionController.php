<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = Question::with('subject')->latest()->paginate(15);
        return view('corse.question.index', compact('questions'));
    }

    public function create()
    {
        $subjects = Subject::orderBy('name')->get();
        return view('corse.question.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id'    => 'required|exists:subjects,id',
            'question'      => 'required|string',
            'question_type' => 'required|in:mcq,msq,descripted',
            'answers'       => 'nullable|array|min:2',
            'answers.*'     => 'required|string|max:500',
            'is_correct'    => 'nullable|array',
            'is_correct.*'  => 'in:0,1',
        ]);

        // Create the question first
        $question = Question::create(
            $request->only('subject_id', 'question', 'question_type')
        );

        // Save each answer option
        $answers    = $request->input('answers', []);
        $isCorrects = $request->input('is_correct', []);

        foreach ($answers as $idx => $answerText) {
            if (trim($answerText) === '') continue;

            Answer::create([
                'question_id' => $question->id,
                'answer'      => $answerText,
                'is_correct'  => ($isCorrects[$idx] ?? 0) == 1,
            ]);
        }

        return redirect()->route('admin.questions.index')
                         ->with('success', 'Question and answers saved successfully.');
    }

    public function edit(Question $question)
    {
        $subjects = Subject::orderBy('name')->get();
        // Load answers so the edit view can pre-fill them
        $question->load('answers');
        return view('corse.question.edit', compact('question', 'subjects'));
    }

    public function update(Request $request, Question $question)
    {
        $request->validate([
            'subject_id'    => 'required|exists:subjects,id',
            'question'      => 'required|string',
            'question_type' => 'required|in:mcq,msq,descripted',
            'answers'       => 'nullable|array|min:2',
            'answers.*'     => 'required|string|max:500',
            'is_correct'    => 'nullable|array',
            'is_correct.*'  => 'in:0,1',
        ]);

        // Update the question itself
        $question->update(
            $request->only('subject_id', 'question', 'question_type')
        );

        // ── Upsert answers ────────────────────────────────────────────
        // answer_ids[] = existing DB id (or empty string for new rows)
        $answers    = $request->input('answers', []);
        $isCorrects = $request->input('is_correct', []);
        $answerIds  = $request->input('answer_ids', []);

        // Track which existing IDs we are keeping
        $keptIds = [];

        foreach ($answers as $idx => $answerText) {
            if (trim($answerText) === '') continue;

            $existingId = $answerIds[$idx] ?? null;

            if ($existingId) {
                // Update existing row
                $answer = Answer::find($existingId);
                if ($answer && $answer->question_id === $question->id) {
                    $answer->update([
                        'answer'     => $answerText,
                        'is_correct' => ($isCorrects[$idx] ?? 0) == 1,
                    ]);
                    $keptIds[] = $answer->id;
                }
            } else {
                // New answer row — create it
                $new = Answer::create([
                    'question_id' => $question->id,
                    'answer'      => $answerText,
                    'is_correct'  => ($isCorrects[$idx] ?? 0) == 1,
                ]);
                $keptIds[] = $new->id;
            }
        }

        // Delete any answers that were removed in the UI
        Answer::where('question_id', $question->id)
              ->whereNotIn('id', $keptIds)
              ->delete();

        return redirect()->route('admin.questions.index')
                         ->with('success', 'Question and answers updated successfully.');
    }

    public function destroy(Question $question)
    {
        $question->delete();
        return redirect()->route('admin.questions.index')
                         ->with('success', 'Question deleted successfully.');
    }
}

