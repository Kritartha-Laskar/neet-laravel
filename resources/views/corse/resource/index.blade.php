@extends('layouts.admin')
@section('title', 'Manage Resources')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border: none; border-radius: 15px;">
            <div class="card-body text-white p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h3 class="fw-bold mb-1">Resource Repository</h3>
                        <p class="mb-0 opacity-75">Upload, manage, and distribute videos, PDFs, and images via API.</p>
                    </div>
                    <a href="{{ route('admin.resources.create') }}" class="btn btn-light btn-lg fw-semibold mt-2 mt-md-0">
                        <i class="icon-cloud-upload me-2"></i> Upload New Resource
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Stats Row --}}
<div class="row mb-4">
    <div class="col-md-4 grid-margin stretch-card">
        <div class="card text-center shadow-sm border-0">
            <div class="card-body">
                <div class="icon-box bg-light-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: rgba(102, 126, 234, 0.1);">
                    <i class="icon-film text-primary" style="font-size: 1.8rem;"></i>
                </div>
                <h4 class="fw-bold text-dark">{{ $videos->count() }}</h4>
                <p class="text-muted mb-0">Videos Uploaded</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 grid-margin stretch-card">
        <div class="card text-center shadow-sm border-0">
            <div class="card-body">
                <div class="icon-box bg-light-danger rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: rgba(245, 101, 101, 0.1);">
                    <i class="icon-doc text-danger" style="font-size: 1.8rem;"></i>
                </div>
                <h4 class="fw-bold text-dark">{{ $pdfs->count() }}</h4>
                <p class="text-muted mb-0">PDF Documents</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 grid-margin stretch-card">
        <div class="card text-center shadow-sm border-0">
            <div class="card-body">
                <div class="icon-box bg-light-success rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: rgba(72, 187, 120, 0.1);">
                    <i class="icon-picture text-success" style="font-size: 1.8rem;"></i>
                </div>
                <h4 class="fw-bold text-dark">{{ $images->count() }}</h4>
                <p class="text-muted mb-0">Images & Assets</p>
            </div>
        </div>
    </div>
</div>

{{-- Resources Tabs & Lists --}}
<div class="row">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <ul class="nav nav-tabs nav-tabs-custom mb-4" id="resourceTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-semibold" id="videos-tab" data-toggle="tab" data-target="#videos" type="button" role="tab" aria-controls="videos" aria-selected="true">
                            <i class="icon-film me-1"></i> Videos
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="pdfs-tab" data-toggle="tab" data-target="#pdfs" type="button" role="tab" aria-controls="pdfs" aria-selected="false">
                            <i class="icon-doc me-1"></i> PDFs
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="images-tab" data-toggle="tab" data-target="#images" type="button" role="tab" aria-controls="images" aria-selected="false">
                            <i class="icon-picture me-1"></i> Images
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="resourceTabsContent">
                    {{-- ── 1. Videos Tab ── --}}
                    <div class="tab-pane fade show active" id="videos" role="tabpanel" aria-labelledby="videos-tab">
                        @if($videos->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="icon-film d-block mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                                <p class="mb-0">No videos found. Upload one to get started!</p>
                            </div>
                        @else
                            <div class="row g-4">
                                @foreach($videos as $video)
                                <div class="col-md-4">
                                    <div class="card border h-100 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                                        <div class="position-relative bg-dark" style="height: 180px;">
                                            @if($video->thumbnail_url)
                                                <img src="{{ $video->thumbnail_url }}" class="w-100 h-100 object-fit-cover" alt="{{ $video->title }}" style="object-fit: cover;">
                                            @else
                                                <div class="d-flex align-items-center justify-content-center w-100 h-100 bg-secondary text-white-50">
                                                    <i class="icon-control-play" style="font-size: 3rem;"></i>
                                                </div>
                                            @endif
                                            <span class="position-absolute bottom-0 right-0 bg-dark text-white px-2 py-1 m-2 rounded small" style="right: 10px; bottom: 10px; font-size: 10px;">
                                                {{ $video->file_size_human }}
                                            </span>
                                        </div>
                                        <div class="card-body d-flex flex-column justify-content-between p-3">
                                            <div>
                                                <h5 class="fw-bold text-dark mb-1">{{ Str::limit($video->title, 40) }}</h5>
                                                @if($video->subject)
                                                    <span class="badge badge-success mb-2">{{ $video->subject }}</span>
                                                @endif
                                                <p class="text-muted small mb-3">{{ Str::limit($video->description ?? 'No description provided.', 80) }}</p>
                                            </div>
                                            <div>
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="small text-muted">Status:</span>
                                                    <form action="{{ route('admin.resources.toggle-status', $video->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-{{ $video->is_active ? 'success' : 'secondary' }} py-1">
                                                            {{ $video->is_active ? 'Active' : 'Inactive' }}
                                                        </button>
                                                    </form>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ $video->file_url }}" target="_blank" class="btn btn-outline-primary btn-sm flex-fill">
                                                        <i class="icon-link"></i> Play
                                                    </a>
                                                    <form action="{{ route('admin.resources.destroy', $video->id) }}" method="POST" class="flex-fill" onsubmit="return confirm('Delete this video?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm w-100">
                                                            <i class="icon-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- ── 2. PDFs Tab ── --}}
                    <div class="tab-pane fade" id="pdfs" role="tabpanel" aria-labelledby="pdfs-tab">
                        @if($pdfs->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="icon-doc d-block mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                                <p class="mb-0">No PDFs found. Upload one to get started!</p>
                            </div>
                        @else
                            <div class="table-responsive border rounded">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Title</th>
                                            <th>Subject</th>
                                            <th>File Size</th>
                                            <th>Uploaded At</th>
                                            <th>Status</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pdfs as $pdf)
                                        <tr>
                                            <td class="align-middle">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="icon-doc text-danger fs-4"></i>
                                                    <div>
                                                        <div class="fw-semibold text-dark">{{ $pdf->title }}</div>
                                                        <small class="text-muted">{{ $pdf->file_name }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                @if($pdf->subject)
                                                    <span class="badge badge-info">{{ $pdf->subject }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-muted">{{ $pdf->file_size_human }}</td>
                                            <td class="align-middle text-muted">{{ $pdf->created_at->format('d M Y') }}</td>
                                            <td class="align-middle">
                                                <form action="{{ route('admin.resources.toggle-status', $pdf->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-xs btn-{{ $pdf->is_active ? 'success' : 'secondary' }}">
                                                        {{ $pdf->is_active ? 'Active' : 'Inactive' }}
                                                    </button>
                                                </form>
                                            </td>
                                            <td class="text-end align-middle">
                                                <a href="{{ $pdf->file_url }}" target="_blank" class="btn btn-outline-primary btn-sm me-1">
                                                    <i class="icon-eye"></i> View PDF
                                                </a>
                                                <form action="{{ route('admin.resources.destroy', $pdf->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this PDF?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="icon-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    {{-- ── 3. Images Tab ── --}}
                    <div class="tab-pane fade" id="images" role="tabpanel" aria-labelledby="images-tab">
                        @if($images->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="icon-picture d-block mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                                <p class="mb-0">No images found. Upload one to get started!</p>
                            </div>
                        @else
                            <div class="row g-3">
                                @foreach($images as $image)
                                <div class="col-md-3 col-sm-6">
                                    <div class="card border h-100 shadow-sm" style="border-radius: 10px; overflow: hidden;">
                                        <div class="position-relative bg-light" style="height: 150px;">
                                            <img src="{{ $image->file_url }}" class="w-100 h-100 object-fit-cover" alt="{{ $image->title }}" style="object-fit: cover;">
                                        </div>
                                        <div class="card-body p-2 d-flex flex-column justify-content-between">
                                            <div>
                                                <h6 class="fw-bold text-dark mb-1 text-truncate">{{ $image->title }}</h6>
                                                <small class="text-muted d-block mb-2">{{ $image->file_size_human }}</small>
                                            </div>
                                            <div class="d-flex gap-1">
                                                <a href="{{ $image->file_url }}" target="_blank" class="btn btn-outline-primary btn-sm py-1 px-2 flex-fill">
                                                    <i class="icon-size-fullscreen"></i>
                                                </a>
                                                <form action="{{ route('admin.resources.destroy', $image->id) }}" method="POST" class="flex-fill" onsubmit="return confirm('Delete this image?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm py-1 px-2 w-100">
                                                        <i class="icon-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .nav-tabs-custom .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: #6c757d;
        padding: 10px 20px;
    }
    .nav-tabs-custom .nav-link.active {
        border-bottom: 3px solid #1e3c72;
        color: #1e3c72;
        background: transparent;
    }
</style>
@endpush
