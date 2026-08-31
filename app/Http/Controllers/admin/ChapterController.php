<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\CourseName;
use App\Models\Subject;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    /**
     * Display a listing of chapters with filter options.
     */
    public function index(Request $request)
    {
        $query = Chapter::with(['subject', 'course'])
            ->withCount(['questions', 'questionPapers'])
            ->orderBy('sort_order')
            ->latest();

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->query('subject_id'));
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->query('course_id'));
        }

        $chapters = $query->paginate(20);
        $courses  = CourseName::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('corse.chapter.index', compact('chapters', 'courses', 'subjects'));
    }

    /**
     * Show the form for creating a new chapter.
     */
    public function create()
    {
        $courses  = CourseName::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('corse.chapter.create', compact('courses', 'subjects'));
    }

    /**
     * Store a newly created chapter in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id'     => 'required|exists:subjects,id',
            'course_id'      => 'nullable|exists:course_names,id',
            'chapter_number' => 'nullable|string|max:50',
            'name'           => 'required|string|max:191',
            'description'    => 'nullable|string|max:1000',
            'sort_order'     => 'nullable|integer|min:0',
        ]);

        // Auto assign course_id from subject if not provided
        if (empty($validated['course_id'])) {
            $subject = Subject::find($validated['subject_id']);
            if ($subject && $subject->course_id) {
                $validated['course_id'] = $subject->course_id;
            }
        }

        Chapter::create($validated);

        return redirect()->route('admin.chapters.index')
            ->with('success', 'Chapter created successfully!');
    }

    /**
     * Show the form for editing the specified chapter.
     */
    public function edit(Chapter $chapter)
    {
        $courses  = CourseName::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('corse.chapter.edit', compact('chapter', 'courses', 'subjects'));
    }

    /**
     * Update the specified chapter in storage.
     */
    public function update(Request $request, Chapter $chapter)
    {
        $validated = $request->validate([
            'subject_id'     => 'required|exists:subjects,id',
            'course_id'      => 'nullable|exists:course_names,id',
            'chapter_number' => 'nullable|string|max:50',
            'name'           => 'required|string|max:191',
            'description'    => 'nullable|string|max:1000',
            'sort_order'     => 'nullable|integer|min:0',
        ]);

        if (empty($validated['course_id'])) {
            $subject = Subject::find($validated['subject_id']);
            if ($subject && $subject->course_id) {
                $validated['course_id'] = $subject->course_id;
            }
        }

        $chapter->update($validated);

        return redirect()->route('admin.chapters.index')
            ->with('success', 'Chapter updated successfully!');
    }

    /**
     * Remove the specified chapter from storage.
     */
    public function destroy(Chapter $chapter)
    {
        $chapter->delete();

        return redirect()->route('admin.chapters.index')
            ->with('success', 'Chapter deleted successfully!');
    }

    /**
     * AJAX endpoint to return JSON array of chapters for a given subject.
     */
    public function bySubject($subjectId)
    {
        $chapters = Chapter::where('subject_id', $subjectId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'chapter_number', 'name']);

        return response()->json($chapters->map(function ($ch) {
            return [
                'id'            => $ch->id,
                'chapter_number'=> $ch->chapter_number,
                'name'          => $ch->name,
                'title'         => $ch->full_title,
            ];
        }));
    }
}
