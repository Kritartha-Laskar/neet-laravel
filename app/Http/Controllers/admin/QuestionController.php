<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = Question::with('subject')->latest()->paginate(15);
        return view('corse.question.index', compact('questions'));
    }

    public function create()
    {
        $courses  = \App\Models\CourseName::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        return view('corse.question.create', compact('subjects', 'courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id'    => 'required|exists:subjects,id',
            'question'      => 'required|string',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'question_type' => 'required|in:mcq,msq,descripted',
            'answers'       => 'nullable|array|min:2',
            'answers.*'     => 'required|string|max:500',
            'is_correct'    => 'nullable|array',
            'is_correct.*'  => 'in:0,1',
        ]);

        $data = $request->only('subject_id', 'question', 'question_type');

        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imageName = time() . '_' . Str::slug(pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME))
                         . '.' . $imageFile->getClientOriginalExtension();
            $data['image'] = $imageFile->storeAs('questions', $imageName, 'public');
        }

        // Create the question first
        $question = Question::create($data);

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
        $courses  = \App\Models\CourseName::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        // Load answers so the edit view can pre-fill them
        $question->load('answers');
        return view('corse.question.edit', compact('question', 'subjects', 'courses'));
    }

    public function update(Request $request, Question $question)
    {
        $request->validate([
            'subject_id'    => 'required|exists:subjects,id',
            'question'      => 'required|string',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'question_type' => 'required|in:mcq,msq,descripted',
            'answers'       => 'nullable|array|min:2',
            'answers.*'     => 'required|string|max:500',
            'is_correct'    => 'nullable|array',
            'is_correct.*'  => 'in:0,1',
        ]);

        $data = $request->only('subject_id', 'question', 'question_type');

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($question->image) {
                Storage::disk('public')->delete($question->image);
            }

            $imageFile = $request->file('image');
            $imageName = time() . '_' . Str::slug(pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME))
                         . '.' . $imageFile->getClientOriginalExtension();
            $data['image'] = $imageFile->storeAs('questions', $imageName, 'public');
        }

        // Update the question itself
        $question->update($data);

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
        if ($question->image) {
            Storage::disk('public')->delete($question->image);
        }

        $question->delete();
        return redirect()->route('admin.questions.index')
                         ->with('success', 'Question deleted successfully.');
    }
}

