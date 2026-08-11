<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\CourseName;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::with('course')->latest()->paginate(15);
        return view('corse.subject.index', compact('subjects'));
    }

    public function create()
    {
        $courses = CourseName::where('status', 'active')->get();
        return view('corse.subject.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:course_names,id',
            'name'      => 'required|string|max:191',
        ]);

        Subject::create($request->only('course_id', 'name'));

        return redirect()->route('admin.subjects.index')
                         ->with('success', 'Subject created successfully.');
    }

    public function edit(Subject $subject)
    {
        $courses = CourseName::where('status', 'active')->get();
        return view('corse.subject.edit', compact('subject', 'courses'));
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'course_id' => 'required|exists:course_names,id',
            'name'      => 'required|string|max:191',
        ]);

        $subject->update($request->only('course_id', 'name'));

        return redirect()->route('admin.subjects.index')
                         ->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return redirect()->route('admin.subjects.index')
                         ->with('success', 'Subject deleted successfully.');
    }
}
