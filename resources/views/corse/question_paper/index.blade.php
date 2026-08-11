@extends('layouts.admin')
@section('title', 'Question Papers')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                
                {{-- Error & Success Alert Banner --}}
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                    <h4 class="card-title mb-0 fw-bold text-dark"><i class="icon-doc me-2 text-primary"></i>All Question Papers</h4>
                    <div class="d-flex gap-2 mt-2 mt-sm-0 align-items-center">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#generatePaperModal" data-bs-toggle="modal" data-bs-target="#generatePaperModal">
                            <i class="icon-magic-wand me-1"></i> Auto-Generate Paper
                        </button>
                        <span class="badge badge-primary p-2">{{ $papers->total() }} Paper(s)</span>
                    </div>
                </div>

                @if($papers->isEmpty())
                    <div class="text-center py-5 text-muted border rounded bg-light">
                        <i class="icon-doc d-block mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                        <h5>No Question Papers Found</h5>
                        <p class="mb-3">Run seeders to import default papers or click "Auto-Generate Paper" to build one instantly from the question bank.</p>
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#generatePaperModal" data-bs-toggle="modal" data-bs-target="#generatePaperModal">
                            <i class="icon-magic-wand me-1"></i> Auto-Generate Now
                        </button>
                    </div>
                @else
                <div class="row g-4">
                    @foreach($papers as $paper)
                    <div class="col-md-4 mb-4">
                        <div class="card border shadow-sm h-100" style="border-radius:12px; overflow:hidden;">
                            {{-- Coloured header strip --}}
                            <div style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); padding:20px 24px 16px;">
                                <h5 class="text-white fw-bold mb-1">{{ $paper->title }}</h5>
                                <small class="text-white opacity-75">{{ $paper->exam_name }} · {{ $paper->exam_year }}</small>
                            </div>

                            <div class="card-body d-flex flex-column justify-content-between p-4">
                                <div>
                                    <p class="text-muted small mb-3">{{ Str::limit($paper->description ?? 'No description available.', 120) }}</p>

                                    {{-- Stats row --}}
                                    <div class="row text-center mb-3 g-0 border rounded py-2 bg-light">
                                        <div class="col-4 border-end">
                                            <div class="fw-bold fs-5 text-primary">{{ $paper->questions_count }}</div>
                                            <small class="text-muted" style="font-size: 10px;">Questions</small>
                                        </div>
                                        <div class="col-4 border-end">
                                            <div class="fw-bold fs-5 text-success">{{ $paper->total_marks }}</div>
                                            <small class="text-muted" style="font-size: 10px;">Marks</small>
                                        </div>
                                        <div class="col-4">
                                            <div class="fw-bold fs-5 text-warning">{{ $paper->duration_minutes }}</div>
                                            <small class="text-muted" style="font-size: 10px;">Minutes</small>
                                        </div>
                                    </div>

                                    {{-- Subject breakdown badges --}}
                                    <div class="d-flex flex-wrap gap-1 mb-4">
                                        <span class="badge badge-success px-2 py-1" style="font-size: 10px;">Biology · 90</span>
                                        <span class="badge badge-info px-2 py-1" style="font-size: 10px;">Physics · 45</span>
                                        <span class="badge badge-warning px-2 py-1" style="font-size: 10px;">Chemistry · 45</span>
                                    </div>
                                </div>

                                <a href="{{ route('admin.question-papers.show', $paper->id) }}"
                                   class="btn btn-primary btn-sm w-100 py-2 fw-semibold">
                                    <i class="icon-eye me-1"></i> View Paper
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4">{{ $papers->links() }}</div>
                @endif

            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════
     MODAL: AUTO-GENERATE QUESTION PAPER
     NEET structure (Biology: 90, Physics: 45, Chemistry: 45)
══════════════════════════════════════════════════════ -->
<div class="modal fade" id="generatePaperModal" tabindex="-1" role="dialog" aria-labelledby="generatePaperModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.question-papers.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="generatePaperModalLabel"><i class="icon-magic-wand text-primary me-2"></i>Auto-Generate Question Paper</h5>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2" style="font-size: 12px;">
                        <i class="icon-info me-1"></i> Generates a standard NEET Mock Test selecting random questions from Biology (90), Physics (45), and Chemistry (45) from your question repository.
                    </div>

                    <div class="form-group mb-3">
                        <label for="paper_title" class="fw-semibold">Paper Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="paper_title" class="form-control" placeholder="e.g. NEET Mock Test — Paper 4" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="paper_desc" class="fw-semibold">Description</label>
                        <textarea name="description" id="paper_desc" rows="3" class="form-control" placeholder="Brief description of the test paper..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="exam_name" class="fw-semibold">Exam Name</label>
                                <input type="text" name="exam_name" id="exam_name" class="form-control" value="NEET">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="exam_year" class="fw-semibold">Exam Year</label>
                                <input type="number" name="exam_year" id="exam_year" class="form-control" value="{{ date('Y') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="duration_minutes" class="fw-semibold">Duration (Minutes)</label>
                                <input type="number" name="duration_minutes" id="duration_minutes" class="form-control" value="180">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="total_marks" class="fw-semibold">Total Marks</label>
                                <input type="number" name="total_marks" id="total_marks" class="form-control" value="720">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-semibold"><i class="icon-magic-wand me-1"></i> Generate Paper</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
