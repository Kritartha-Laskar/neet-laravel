<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\CourseName;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = CourseName::latest()->paginate(15);
        return view('corse.course.index', compact('courses'));
    }

    public function create()
    {
        return view('corse.course.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:191|unique:course_names,name',
            'status' => 'required|in:active,inactive',
        ]);

        CourseName::create($request->only('name', 'status'));

        return redirect()->route('admin.courses.index')
                         ->with('success', 'Course created successfully.');
    }

    public function edit(CourseName $course)
    {
        return view('corse.course.edit', compact('course'));
    }

    public function update(Request $request, CourseName $course)
    {
        $request->validate([
            'name'   => 'required|string|max:191|unique:course_names,name,' . $course->id,
            'status' => 'required|in:active,inactive',
        ]);

        $course->update($request->only('name', 'status'));

        return redirect()->route('admin.courses.index')
                         ->with('success', 'Course updated successfully.');
    }

    public function destroy(CourseName $course)
    {
        $course->delete();
        return redirect()->route('admin.courses.index')
                         ->with('success', 'Course deleted successfully.');
    }
}
