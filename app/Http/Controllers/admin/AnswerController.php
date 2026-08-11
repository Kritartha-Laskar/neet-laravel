<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    public function index()
    {
        $answers = Answer::with('question')->latest()->paginate(15);
        return view('corse.answer.index', compact('answers'));
    }

    public function create()
    {
        $questions = Question::latest()->get();
        return view('corse.answer.create', compact('questions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer'      => 'required|string',
            'is_correct'  => 'required|in:0,1',
        ]);

        Answer::create([
            'question_id' => $request->question_id,
            'answer'      => $request->answer,
            'is_correct'  => $request->is_correct,
        ]);

        return redirect()->route('admin.answers.index')
                         ->with('success', 'Answer created successfully.');
    }

    public function edit(Answer $answer)
    {
        $questions = Question::latest()->get();
        return view('corse.answer.edit', compact('answer', 'questions'));
    }

    public function update(Request $request, Answer $answer)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer'      => 'required|string',
            'is_correct'  => 'required|in:0,1',
        ]);

        $answer->update([
            'question_id' => $request->question_id,
            'answer'      => $request->answer,
            'is_correct'  => $request->is_correct,
        ]);

        return redirect()->route('admin.answers.index')
                         ->with('success', 'Answer updated successfully.');
    }

    public function destroy(Answer $answer)
    {
        $answer->delete();
        return redirect()->route('admin.answers.index')
                         ->with('success', 'Answer deleted successfully.');
    }
}
