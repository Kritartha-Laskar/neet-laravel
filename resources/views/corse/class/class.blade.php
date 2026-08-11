@extends('layouts.admin')

@section('title', 'Class Folders & Materials')

@push('styles')
<style>
    .folder-card {
        transition: all 0.25s ease-in-out;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        background: #ffffff;
        cursor: pointer;
    }
    .folder-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        border-color: #7da5pt;
    }
    .folder-card.active-folder {
        border-color: #4B49AC;
        background: #f8f9ff;
        box-shadow: 0 4px 15px rgba(75, 73, 172, 0.15);
    }
    .folder-icon {
        font-size: 2.2rem;
        color: #ffc107;
    }
    .folder-card.active-folder .folder-icon {
        color: #4B49AC;
    }
    .btn-purple {
        background-color: #8f5fe8;
        border-color: #8f5fe8;
        color: #ffffff;
    }
    .btn-purple:hover {
        background-color: #7a46db;
        border-color: #7a46db;
        color: #ffffff;
    }
    .playlist-item-card {
        border-left: 4px solid #4B49AC;
        transition: all 0.2s ease;
    }
    .playlist-item-card:hover {
        background-color: #fcfcfd;
    }
</style>
@endpush

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-1 text-dark">
                    <i class="icon-folder me-2 text-warning"></i>Class Folders &amp; Material Ordering
                </h3>
                <p class="text-muted mb-0 small">Organize classes as folders, arrange video/PDF/image materials order, create MCQ questions, and view full class playlists.</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addClassFolderModal">
                    <i class="icon-folder-alt me-1"></i> New Class Folder
                </button>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     SECTION 1: CLASS FOLDERS OVERVIEW (Shown as folders)
========================================================================= -->
<div class="row mb-4">
    @forelse($classes as $c)
        <div class="col-6 col-sm-4 col-md-3 col-xl-2 mb-3">
            <a href="{{ route('admin.classes.index', ['class_id' => $c->id]) }}" class="text-decoration-none text-dark">
                <div class="folder-card p-3 text-center h-100 {{ optional($selectedClass)->id === $c->id ? 'active-folder' : '' }}">
                    <div class="mb-2">
                        <i class="{{ optional($selectedClass)->id === $c->id ? 'icon-folder-alt' : 'icon-folder' }} folder-icon"></i>
                    </div>
                    <h6 class="fw-bold mb-1 text-truncate" title="{{ $c->name }}">{{ $c->name }}</h6>
                    <span class="badge bg-light text-dark border me-1 small">{{ $c->resources_count }} Materials</span>
                    <span class="badge bg-purple text-white small">{{ $c->questions_count }} MCQs</span>
                </div>
            </a>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info text-center py-4">
                <i class="icon-folder d-block mb-2" style="font-size: 2.5rem;"></i>
                <h5>No Class Folders Created Yet</h5>
                <p class="mb-2">Click "New Class Folder" above to create your first class folder (e.g. Class 1).</p>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addClassFolderModal">
                    <i class="icon-plus me-1"></i> Create Class Folder
                </button>
            </div>
        </div>
    @endforelse
</div>

@if($selectedClass)
<!-- =========================================================================
     SECTION 2: SELECTED CLASS MATERIALS TABLE & SERIAL ORDERING
========================================================================= -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h4 class="mb-0 fw-bold text-dark fs-4">{{ strtolower($selectedClass->name) }}</h4>
                <span class="badge btn-purple px-3 py-2 rounded-pill">{{ $selectedClass->resources->count() + $selectedClass->questions->count() }} Materials &amp; MCQs</span>
            </div>
            <p class="text-muted mb-0 small mt-1">{{ strtolower($selectedClass->description ?? 'No subject/description specified.') }}</p>
        </div>
        <div class="d-flex align-items-center gap-2 mt-2 mt-sm-0">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editClassModal" title="Edit Folder Info">
                <i class="icon-pencil"></i>
            </button>
            <form action="{{ route('admin.classes.destroy', $selectedClass->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete {{ $selectedClass->name }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete Folder">
                    <i class="icon-trash"></i>
                </button>
            </form>
            <div class="vr mx-1"></div>
            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#assignMaterialModal">
                <i class="icon-link me-1"></i> Assign Material
            </button>
            <button type="button" class="btn btn-purple btn-sm" data-bs-toggle="modal" data-bs-target="#createMcqModal">
                <i class="icon-question me-1"></i> Create MCQ Question
            </button>
        </div>
    </div>

    <div class="card-body p-0">
        @if($selectedClass->resources->isEmpty() && $selectedClass->questions->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="icon-doc d-block mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                <h5>No Materials or Questions in {{ $selectedClass->name }}</h5>
                <p class="mb-0">Click "Assign Material" or "Create MCQ Question" above to add items to this class playlist.</p>
            </div>
        @else
            <!-- SERIAL ORDERING TABLE -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 110px;" class="ps-3">Serial / Order</th>
                            <th>Material Title / Prompt</th>
                            <th>Type</th>
                            <th>Subject</th>
                            <th>File Size / Info</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- 1. Class Resources (Videos, PDFs, Images) --}}
                        @foreach($selectedClass->resources as $res)
                            <tr>
                                <td class="ps-3">
                                    <input type="number" class="form-control form-control-sm res-sort-input text-center fw-bold" 
                                           data-id="{{ $res->id }}" value="{{ $res->sort_order }}" style="width: 75px;">
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $res->title }}</div>
                                    <small class="text-muted text-truncate d-inline-block" style="max-width: 320px;">{{ $res->file_name }}</small>
                                </td>
                                <td>
                                    @if($res->type === 'video')
                                        <span class="badge bg-info text-white"><i class="icon-film me-1"></i> Video</span>
                                    @elseif($res->type === 'pdf')
                                        <span class="badge bg-danger"><i class="icon-doc me-1"></i> PDF</span>
                                    @else
                                        <span class="badge bg-success"><i class="icon-picture me-1"></i> Image</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted">{{ $res->subject ?? '—' }}</span>
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $res->file_size_human }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-2">
                                        @if($res->type === 'video')
                                            <button type="button" class="btn btn-outline-info btn-xs play-video-btn" 
                                                    data-bs-toggle="modal" data-bs-target="#videoPlayerModal"
                                                    data-url="{{ $res->file_url }}" 
                                                    data-title="{{ $res->title }}">
                                                <i class="icon-control-play me-1"></i> Play
                                            </button>
                                        @else
                                            <a href="{{ $res->file_url }}" target="_blank" class="btn btn-outline-primary btn-xs">
                                                <i class="icon-link me-1"></i> View
                                            </a>
                                        @endif
                                        <form action="{{ route('admin.classes.remove-resource', $res->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-xs" title="Unassign from class">
                                                <i class="icon-close me-1"></i> Unassign
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        {{-- 2. Class MCQ Questions --}}
                        @foreach($selectedClass->questions as $q)
                            <tr class="table-purple-subtle">
                                <td class="ps-3">
                                    <input type="number" class="form-control form-control-sm q-sort-input text-center fw-bold" 
                                           data-id="{{ $q->id }}" value="{{ $q->sort_order }}" style="width: 75px; border-color:#8f5fe8;">
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><i class="icon-question me-1 text-purple"></i> {{ $q->question }}</div>
                                    <small class="text-muted">{{ $q->answers->count() }} Options (MCQ)</small>
                                </td>
                                <td>
                                    <span class="badge btn-purple"><i class="icon-question me-1"></i> MCQ Question</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ optional($q->subject)->name ?? '—' }}</span>
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $q->answers->count() }} Choices</span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-2">
                                        <button type="button" class="btn btn-outline-purple btn-xs preview-mcq-btn"
                                                data-bs-toggle="modal" data-bs-target="#previewMcqModal{{ $q->id }}">
                                            <i class="icon-eye me-1"></i> View Question
                                        </button>
                                        <form action="{{ route('admin.classes.destroy-question', $q->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove MCQ question?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-xs">
                                                <i class="icon-trash me-1"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- SAVE SERIALIZATION ORDER BUTTON -->
            <div class="p-3 bg-light d-flex justify-content-between align-items-center rounded-bottom">
                <small class="text-muted"><i class="icon-info me-1"></i> Adjust Serial / Order numbers (1, 2, 3...) and click save to update playlist sequence.</small>
                <button type="button" class="btn btn-secondary btn-md fw-bold px-4 id-save-serialization-btn">
                    <i class="icon-refresh me-1"></i> Save Serialization Order
                </button>
            </div>
        @endif
    </div>
</div>

<!-- =========================================================================
     SECTION 3: WHOLE PLAYLIST VIEW FOR SELECTED CLASS (e.g. Class 1 Playlist)
========================================================================= -->
@php
    // Merge resources and questions into a single collection sorted by sort_order
    $playlistItems = collect();

    foreach($selectedClass->resources as $r) {
        $playlistItems->push((object)[
            'item_type'  => 'resource',
            'sort_order' => $r->sort_order,
            'data'       => $r,
        ]);
    }

    foreach($selectedClass->questions as $q) {
        $playlistItems->push((object)[
            'item_type'  => 'question',
            'sort_order' => $q->sort_order,
            'data'       => $q,
        ]);
    }

    $playlistItems = $playlistItems->sortBy('sort_order')->values();
@endphp

<div class="card shadow-sm border-0 mb-5" style="border-radius: 12px;">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-dark">
            <i class="icon-playlist me-2 text-primary"></i>Whole Playlist for {{ $selectedClass->name }}
        </h5>
        <span class="badge bg-primary px-3 py-2">{{ $playlistItems->count() }} Total Playlist Items</span>
    </div>
    <div class="card-body">
        @if($playlistItems->isEmpty())
            <p class="text-muted text-center py-3 mb-0">Playlist is empty. Add materials or questions above.</p>
        @else
            <div class="list-group list-group-flush border rounded">
                @foreach($playlistItems as $index => $item)
                    @if($item->item_type === 'resource')
                        @php $r = $item->data; @endphp
                        <div class="list-group-item p-3 playlist-item-card d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge bg-dark text-white rounded-circle p-2 fs-6" style="width: 38px; height: 38px; display:flex; align-items:center; justify-content:center;">
                                    {{ $r->sort_order }}
                                </span>
                                <div>
                                    <div class="fw-bold text-dark">{{ $r->title }}</div>
                                    <div class="small text-muted">
                                        Type: <span class="fw-semibold text-uppercase me-2">{{ $r->type }}</span>
                                        Size: <span class="me-2">{{ $r->file_size_human }}</span>
                                        Subject: <span class="fw-semibold">{{ $r->subject ?? '—' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                @if($r->type === 'video')
                                    <button type="button" class="btn btn-primary btn-sm play-video-btn" 
                                            data-bs-toggle="modal" data-bs-target="#videoPlayerModal"
                                            data-url="{{ $r->file_url }}" data-title="{{ $r->title }}">
                                        <i class="icon-control-play me-1"></i> Stream Video
                                    </button>
                                @else
                                    <a href="{{ $r->file_url }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                        <i class="icon-eye me-1"></i> Open Document
                                    </a>
                                @endif
                            </div>
                        </div>
                    @else
                        @php $q = $item->data; @endphp
                        <div class="list-group-item p-3 playlist-item-card border-start-purple d-flex justify-content-between align-items-center flex-wrap gap-2 bg-light">
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge btn-purple rounded-circle p-2 fs-6" style="width: 38px; height: 38px; display:flex; align-items:center; justify-content:center;">
                                    {{ $q->sort_order }}
                                </span>
                                <div>
                                    <div class="fw-bold text-dark">
                                        <i class="icon-question me-1 text-purple"></i> MCQ Assessment: {{ $q->question }}
                                    </div>
                                    <div class="small text-muted">
                                        Subject: <span class="fw-semibold me-2">{{ optional($q->subject)->name ?? 'General' }}</span>
                                        Total Options: <span class="fw-semibold">{{ $q->answers->count() }}</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <button type="button" class="btn btn-purple btn-sm" data-bs-toggle="modal" data-bs-target="#previewMcqModal{{ $q->id }}">
                                    <i class="icon-check me-1"></i> Take / Test MCQ
                                </button>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</div>
@endif

<!-- =========================================================================
     MODALS SECTION
========================================================================= -->

<!-- 1. MODAL: ADD CLASS FOLDER -->
<div class="modal fade" id="addClassFolderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.classes.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="icon-folder me-2 text-warning"></i>Create New Class Folder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Class Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Class 1, Class 2, Biology Class" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Subject / Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="e.g. biology, physics"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Folder Display Order</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ $classes->count() + 1 }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Class Folder</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($selectedClass)
<!-- 2. MODAL: EDIT CLASS FOLDER -->
<div class="modal fade" id="editClassModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.classes.update', $selectedClass->id) }}">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit {{ $selectedClass->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Class Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ $selectedClass->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Subject / Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ $selectedClass->description }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Display Order</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ $selectedClass->sort_order }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 3. MODAL: ASSIGN / UPLOAD MATERIAL -->
<div class="modal fade" id="assignMaterialModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header pb-0 border-0">
                <h5 class="modal-title fw-bold"><i class="icon-link me-2 text-success"></i>Add Material to {{ $selectedClass->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="px-3 pt-2">
                <ul class="nav nav-pills nav-fill bg-light p-1 rounded" id="materialTabs">
                    <li class="nav-item">
                        <button type="button" class="nav-link active py-2 border-0 w-100" id="tab-assign-ex" onclick="switchMaterialTab('assign')">
                            Assign Existing
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link py-2 border-0 w-100" id="tab-upload-new" onclick="switchMaterialTab('upload')">
                            Upload New File
                        </button>
                    </li>
                </ul>
            </div>

            <div class="modal-body">
                <!-- SUB-TAB 1: ASSIGN EXISTING -->
                <div id="pane-assign-ex">
                    <form method="POST" action="{{ route('admin.classes.assign-resource') }}">
                        @csrf
                        <input type="hidden" name="study_class_id" value="{{ $selectedClass->id }}">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Select Material <span class="text-danger">*</span></label>
                            <select name="resource_id" class="form-select" required>
                                <option value="">-- Choose Material --</option>
                                @foreach($unassignedResources as $u)
                                    <option value="{{ $u->id }}">{{ $u->title }} ({{ strtoupper($u->type) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Serial Position Order</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ $selectedClass->resources->count() + 1 }}">
                        </div>
                        <div class="modal-footer px-0 pb-0 border-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success" {{ $unassignedResources->isEmpty() ? 'disabled' : '' }}>Assign Material</button>
                        </div>
                    </form>
                </div>

                <!-- SUB-TAB 2: UPLOAD NEW MATERIAL -->
                <div id="pane-upload-new" class="d-none">
                    <form method="POST" action="{{ route('admin.classes.upload-resource') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="study_class_id" value="{{ $selectedClass->id }}">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Material Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="video" selected>Video File (MP4, AVI, WebM)</option>
                                <option value="pdf">PDF Document</option>
                                <option value="image">Image (JPEG, PNG, WebP)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Material Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Lecture 1 Video" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Select File <span class="text-danger">*</span></label>
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Subject (Optional)</label>
                            <input type="text" name="subject" class="form-control" placeholder="e.g. biology" value="{{ $selectedClass->description }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Serial Position Order</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ $selectedClass->resources->count() + 1 }}">
                        </div>
                        <div class="modal-footer px-0 pb-0 border-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Upload &amp; Assign</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 4. MODAL: CREATE MCQ QUESTION -->
<div class="modal fade" id="createMcqModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.classes.store-question') }}">
                @csrf
                <input type="hidden" name="study_class_id" value="{{ $selectedClass->id }}">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="icon-question me-2 text-purple"></i>Create MCQ Question for {{ $selectedClass->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">Question Text / Prompt <span class="text-danger">*</span></label>
                            <textarea name="question" class="form-control" rows="3" placeholder="Enter MCQ question prompt..." required></textarea>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Subject</label>
                            <select name="subject_id" class="form-select">
                                <option value="">-- Select Subject --</option>
                                @foreach($subjects as $subj)
                                    <option value="{{ $subj->id }}">{{ $subj->name }}</option>
                                @endforeach
                            </select>
                            <div class="mt-3">
                                <label class="form-label fw-bold">Serial / Order Position</label>
                                <input type="number" name="sort_order" class="form-control" value="{{ $selectedClass->questions->count() + 1 }}">
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold mt-2 mb-3 text-purple">Answer Options (Select correct option):</h6>
                    
                    @foreach(['A', 'B', 'C', 'D'] as $index => $label)
                        <div class="input-group mb-2">
                            <div class="input-group-text bg-purple text-white fw-bold">
                                <input class="form-check-input mt-0 me-2" type="radio" name="correct_index" value="{{ $index }}" {{ $index === 0 ? 'checked' : '' }}>
                                Option {{ $label }}
                            </div>
                            <input type="text" name="answers[]" class="form-control" placeholder="Enter option {{ $label }} text" required>
                        </div>
                    @endforeach
                    <small class="text-muted">Radio button marks the correct answer option.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-purple">Save MCQ Question</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 5. MCQ PREVIEW MODALS -->
@foreach($selectedClass->questions as $q)
    <div class="modal fade" id="previewMcqModal{{ $q->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-purple text-white">
                    <h5 class="modal-title fw-bold"><i class="icon-question me-2"></i>MCQ Assessment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5 class="fw-bold mb-3">{{ $q->question }}</h5>
                    <div class="list-group mb-3">
                        @foreach($q->answers as $aIndex => $ans)
                            <div class="list-group-item d-flex justify-content-between align-items-center {{ $ans->is_correct ? 'list-group-item-success' : '' }}">
                                <span><strong>{{ chr(65 + $aIndex) }}.</strong> {{ $ans->answer }}</span>
                                @if($ans->is_correct)
                                    <span class="badge bg-success"><i class="icon-check me-1"></i> Correct Answer</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endforeach

<!-- 6. VIDEO PLAYER MODAL -->
<div class="modal fade" id="videoPlayerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="videoModalTitle">Play Video</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="stopVideoPlayer()"></button>
            </div>
            <div class="modal-body p-0 bg-black text-center">
                <video id="globalVideoPlayer" controls style="max-width: 100%; max-height: 500px; width: 100%;">
                    <source src="" id="globalVideoSource" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    function switchMaterialTab(type) {
        if(type === 'assign') {
            document.getElementById('pane-assign-ex').classList.remove('d-none');
            document.getElementById('pane-upload-new').classList.add('d-none');
            document.getElementById('tab-assign-ex').classList.add('active');
            document.getElementById('tab-upload-new').classList.remove('active');
        } else {
            document.getElementById('pane-assign-ex').classList.add('d-none');
            document.getElementById('pane-upload-new').classList.remove('d-none');
            document.getElementById('tab-assign-ex').classList.remove('active');
            document.getElementById('tab-upload-new').classList.add('active');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Video player handler
        document.querySelectorAll('.play-video-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var url = this.getAttribute('data-url');
                var title = this.getAttribute('data-title');
                
                document.getElementById('videoModalTitle').innerText = title || 'Play Video';
                var player = document.getElementById('globalVideoPlayer');
                var source = document.getElementById('globalVideoSource');
                
                source.src = url;
                player.load();
                player.play();
            });
        });

        // Save serialization order button AJAX
        document.querySelectorAll('.id-save-serialization-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var resourceOrders = {};
                var questionOrders = {};

                document.querySelectorAll('.res-sort-input').forEach(function(input) {
                    var id = input.getAttribute('data-id');
                    resourceOrders[id] = input.value;
                });

                document.querySelectorAll('.q-sort-input').forEach(function(input) {
                    var id = input.getAttribute('data-id');
                    questionOrders[id] = input.value;
                });

                var originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

                fetch("{{ route('admin.classes.reorder') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        resource_orders: resourceOrders,
                        question_orders: questionOrders
                    })
                })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                    if(data.success) {
                        alert('Serialization order updated successfully!');
                        window.location.reload();
                    } else {
                        alert('Failed to update ordering.');
                    }
                })
                .catch(function(err) {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                    alert('Error saving order: ' + err);
                });
            });
        });
    });

    function stopVideoPlayer() {
        var player = document.getElementById('globalVideoPlayer');
        if(player) {
            player.pause();
        }
    }
</script>
@endpush
