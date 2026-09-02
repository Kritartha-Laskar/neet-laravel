@extends('layouts.admin')
@section('title', 'Upload Resource')

@section('content')
<div class="row">
    <div class="col-md-9 mx-auto grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Upload Resource</h4>
                    <a href="{{ route('admin.resources.index') }}" class="btn btn-secondary btn-sm">
                        <i class="icon-arrow-left me-1"></i> Back
                    </a>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.resources.store') }}" enctype="multipart/form-data" id="upload-form">
                    @csrf

                    {{-- Type selector --}}
                    <div class="form-group">
                        <label class="fw-semibold">Resource Type <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3 mt-2" id="type-selector">
                            @foreach([
                                ['value'=>'video', 'icon'=>'icon-film',      'label'=>'Video',  'color'=>'primary'],
                                ['value'=>'pdf',   'icon'=>'icon-doc',       'label'=>'PDF',    'color'=>'danger'],
                                ['value'=>'image', 'icon'=>'icon-picture',   'label'=>'Image',  'color'=>'success'],
                            ] as $t)
                            <label class="type-card flex-fill text-center border rounded p-3 cursor-pointer"
                                   style="cursor:pointer; transition:.2s;"
                                   data-type="{{ $t['value'] }}">
                                <input type="radio" name="type" value="{{ $t['value'] }}" class="d-none"
                                       {{ old('type') === $t['value'] ? 'checked' : '' }} required>
                                <i class="{{ $t['icon'] }} d-block mb-1" style="font-size:2rem;"></i>
                                <span class="fw-semibold">{{ $t['label'] }}</span>
                            </label>
                            @endforeach
                        </div>
                        @error('type')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>

                    {{-- Title --}}
                    <div class="form-group">
                        <label for="title">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title"
                               class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title') }}" placeholder="Enter a descriptive title" required>
                        @error('title')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    {{-- Serial Number / Order --}}
                    <div class="form-group">
                        <label for="sort_order" class="fw-semibold">Serial Number / Order <small class="text-muted">(Optional — e.g. 1, 2, 3)</small></label>
                        <input type="number" name="sort_order" id="sort_order" min="1"
                               class="form-control @error('sort_order') is-invalid @enderror"
                               value="{{ old('sort_order') }}" placeholder="Enter serial number (e.g. 1)">
                        @error('sort_order')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>



                    {{-- File upload — shown based on type --}}
                    <div class="form-group" id="file-upload-section" style="display:none;">
                        <label id="file-label">File <span class="text-danger">*</span></label>

                        {{-- Drop zone --}}
                        <div id="drop-zone" class="border-2 border-dashed rounded p-4 text-center"
                             style="border: 2px dashed #dee2e6; transition:.2s; cursor:pointer;">
                            <i id="drop-icon" class="icon-cloud-upload d-block mb-2" style="font-size:3rem; color:#adb5bd;"></i>
                            <p class="mb-1 fw-semibold text-muted" id="drop-text">Drag & drop your file here</p>
                            <p class="small text-muted mb-3" id="drop-hint">or click to browse</p>
                            <input type="file" name="file" id="file-input" class="d-none" required>
                            <button type="button" class="btn btn-outline-primary btn-sm"
                                    onclick="document.getElementById('file-input').click()">
                                <i class="icon-folder me-1"></i> Browse File
                            </button>
                            <div id="file-preview" class="mt-3" style="display:none;">
                                <span class="badge badge-success p-2" id="file-chosen-name"></span>
                            </div>
                        </div>
                        @error('file')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>

                    {{-- Thumbnail — only for videos --}}
                    <div class="form-group" id="thumbnail-section" style="display:none;">
                        <label for="thumbnail">Video Thumbnail <small class="text-muted">(optional, JPG/PNG/WEBP)</small></label>
                        <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                               class="form-control @error('thumbnail') is-invalid @enderror">
                        @error('thumbnail')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    {{-- Upload progress bar --}}
                    <div id="progress-section" class="mt-3" style="display:none;">
                        <label class="text-muted small">Uploading…</label>
                        <div class="progress" style="height:8px;">
                            <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated"
                                 style="width:0%"></div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary btn-lg me-2" id="submit-btn">
                            <i class="icon-cloud-upload me-1"></i> Upload
                        </button>
                        <a href="{{ route('admin.resources.index') }}" class="btn btn-light btn-lg">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const typeCards   = document.querySelectorAll('.type-card');
    const fileSection = document.getElementById('file-upload-section');
    const thumbSection= document.getElementById('thumbnail-section');
    const fileInput   = document.getElementById('file-input');
    const fileLabel   = document.getElementById('file-label');
    const dropHint    = document.getElementById('drop-hint');
    const filePreview = document.getElementById('file-preview');
    const fileChosen  = document.getElementById('file-chosen-name');
    const dropZone    = document.getElementById('drop-zone');

    const typeConfig = {
        video: { label: 'Video File', hint: 'MP4, AVI, MOV, WEBM — max 500 MB', accept: 'video/*' },
        pdf:   { label: 'PDF File',   hint: 'PDF only — max 50 MB',              accept: 'application/pdf' },
        image: { label: 'Image File', hint: 'JPG, PNG, GIF, WEBP — max 10 MB',  accept: 'image/*' },
    };

    // ── Type card selection ───────────────────────────────────────
    typeCards.forEach(function (card) {
        card.addEventListener('click', function () {
            const type = card.dataset.type;

            // Visual active state
            typeCards.forEach(c => c.classList.remove('border-primary', 'bg-light'));
            card.classList.add('border-primary', 'bg-light');

            // Check the hidden radio
            card.querySelector('input[type=radio]').checked = true;

            // Show file section
            const cfg = typeConfig[type];
            fileLabel.innerHTML = cfg.label + ' <span class="text-danger">*</span>';
            dropHint.textContent = cfg.hint;
            fileInput.accept     = cfg.accept;
            fileSection.style.display = '';

            // Show/hide thumbnail
            thumbSection.style.display = type === 'video' ? '' : 'none';
        });

        // Restore on page load if old() value exists
        if (card.querySelector('input[type=radio]').checked) {
            card.click();
        }
    });

    // ── File input change ─────────────────────────────────────────
    fileInput.addEventListener('change', function () {
        showFileChosen(this.files[0]);
    });

    function showFileChosen(file) {
        if (!file) return;
        filePreview.style.display = '';
        fileChosen.textContent = file.name + ' (' + humanSize(file.size) + ')';
    }

    function humanSize(bytes) {
        if (bytes >= 1073741824) return (bytes / 1073741824).toFixed(2) + ' GB';
        if (bytes >= 1048576)    return (bytes / 1048576).toFixed(2)    + ' MB';
        if (bytes >= 1024)       return (bytes / 1024).toFixed(2)       + ' KB';
        return bytes + ' B';
    }

    // ── Drag & Drop ───────────────────────────────────────────────
    dropZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        dropZone.style.borderColor = '#667eea';
        dropZone.style.background  = '#f0f0ff';
    });

    dropZone.addEventListener('dragleave', function () {
        dropZone.style.borderColor = '#dee2e6';
        dropZone.style.background  = '';
    });

    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropZone.style.borderColor = '#dee2e6';
        dropZone.style.background  = '';
        const file = e.dataTransfer.files[0];
        if (file) {
            // Manually assign to the input via DataTransfer
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
            showFileChosen(file);
        }
    });

    // ── File size validation and progress bar on submit ─────────────
    document.getElementById('upload-form').addEventListener('submit', function (e) {
        const file = fileInput.files[0];
        const selectedTypeInput = document.querySelector('input[name="type"]:checked');
        const selectedType = selectedTypeInput ? selectedTypeInput.value : null;

        if (file && selectedType) {
            let maxSize = 512 * 1024 * 1024; // Default 512 MB for video
            let typeLabel = 'Video';

            if (selectedType === 'pdf') {
                maxSize = 50 * 1024 * 1024; // 50 MB
                typeLabel = 'PDF';
            } else if (selectedType === 'image') {
                maxSize = 10 * 1024 * 1024; // 10 MB
                typeLabel = 'Image';
            }

            if (file.size > maxSize) {
                e.preventDefault();
                alert(`Selected ${typeLabel} file (${humanSize(file.size)}) exceeds the server limit of ${humanSize(maxSize)}. Please select a smaller file or chunk the upload.`);
                return false;
            }
        }

        document.getElementById('progress-section').style.display = '';
        let w = 0;
        const bar = document.getElementById('progress-bar');
        const iv = setInterval(function () {
            w = Math.min(w + Math.random() * 12, 90);
            bar.style.width = w + '%';
            if (w >= 90) clearInterval(iv);
        }, 400);
    });

    // Course -> Subject filtering logic
    const courseIdSelect = document.getElementById('course_id');
    const subjectIdSelect = document.getElementById('subject_id');

    if (courseIdSelect && subjectIdSelect) {
        const allSubjectOptions = Array.from(subjectIdSelect.options);
        
        courseIdSelect.addEventListener('change', function () {
            const selectedCourseId = this.value;
            
            // Clear current options
            subjectIdSelect.innerHTML = '';
            
            // Always add the default option
            const defaultOpt = allSubjectOptions[0];
            subjectIdSelect.appendChild(defaultOpt);
            
            // Filter and append options
            allSubjectOptions.slice(1).forEach(opt => {
                const optCourseId = opt.getAttribute('data-course-id');
                if (!selectedCourseId || optCourseId === selectedCourseId) {
                    subjectIdSelect.appendChild(opt.cloneNode(true));
                }
            });
            
            subjectIdSelect.value = '';
        });

        // Trigger change event to filter initially if old() values are set
        if (courseIdSelect.value) {
            const prevSubjId = subjectIdSelect.value;
            courseIdSelect.dispatchEvent(new Event('change'));
            subjectIdSelect.value = prevSubjId;
        }
    }
})();
</script>
@endpush
