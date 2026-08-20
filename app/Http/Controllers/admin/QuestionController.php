<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Subject;
use App\Models\CourseName;
use App\Models\QuestionPaper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = Question::with('subject')->latest()->paginate(15);
        return view('corse.question.index', compact('questions'));
    }

    public function create(Request $request)
    {
        $courses  = CourseName::orderBy('name')->get();
        $subjects = Subject::with('course')->orderBy('name')->get();

        $selectedCourse  = null;
        $selectedSubject = null;
        $paper           = null;

        if ($request->filled('question_paper_id')) {
            $paper = QuestionPaper::with(['course', 'subject.course'])->find($request->question_paper_id);
            if ($paper) {
                if ($paper->subject) {
                    $selectedSubject = $paper->subject;
                    if ($paper->subject->course) {
                        $selectedCourse = $paper->subject->course;
                    }
                }
                if (!$selectedCourse && $paper->course) {
                    $selectedCourse = $paper->course;
                }
            }
        }

        if (!$selectedCourse && $request->filled('course_id')) {
            $selectedCourse = CourseName::find($request->course_id);
        }

        if (!$selectedSubject && $request->filled('subject_id')) {
            $selectedSubject = Subject::with('course')->find($request->subject_id);
        }

        if ($selectedSubject && !$selectedCourse && $selectedSubject->course) {
            $selectedCourse = $selectedSubject->course;
        }

        return view('corse.question.create', compact('subjects', 'courses', 'selectedCourse', 'selectedSubject', 'paper'));
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

        if ($request->filled('question_paper_id')) {
            $paper = QuestionPaper::find($request->question_paper_id);
            if ($paper) {
                $maxOrder = DB::table('question_paper_question')
                    ->where('question_paper_id', $paper->id)
                    ->max('order') ?? 0;

                DB::table('question_paper_question')->insert([
                    'question_paper_id' => $paper->id,
                    'question_id'       => $question->id,
                    'order'             => $maxOrder + 1,
                    'marks'             => 4,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);

                $totalCount = $paper->questions()->count();
                $paper->update([
                    'total_questions' => $totalCount,
                    'total_marks'     => $totalCount * 4,
                ]);

                return redirect()->route('admin.question-papers.show', $paper->id)
                                 ->with('success', 'Question created and added to question paper successfully.');
            }
        }

        return redirect()->route('admin.questions.index')
                         ->with('success', 'Question and answers saved successfully.');
    }

    public function edit(Question $question)
    {
        $courses  = CourseName::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
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
            if ($question->image) {
                Storage::disk('public')->delete($question->image);
            }
            $imageFile = $request->file('image');
            $imageName = time() . '_' . Str::slug(pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME))
                         . '.' . $imageFile->getClientOriginalExtension();
            $data['image'] = $imageFile->storeAs('questions', $imageName, 'public');
        }

        $question->update($data);

        if ($request->has('answers')) {
            $question->answers()->delete();
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
        }

        return redirect()->route('admin.questions.index')
                         ->with('success', 'Question updated successfully.');
    }

    public function destroy(Question $question)
    {
        if ($question->image) {
            Storage::disk('public')->delete($question->image);
        }
        $question->answers()->delete();
        $question->delete();

        return redirect()->route('admin.questions.index')
                         ->with('success', 'Question deleted successfully.');
    }
}
