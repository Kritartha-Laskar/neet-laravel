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

    // ── Chunked Video Upload Handler for Full Upload Form ─────────────
    const fullForm = document.getElementById('upload-form');
    if (fullForm) {
        fullForm.addEventListener('submit', async function (e) {
            const file = fileInput.files[0];
            const selectedTypeInput = document.querySelector('input[name="type"]:checked');
            const selectedType = selectedTypeInput ? selectedTypeInput.value : null;

            if (file && (selectedType === 'video' || file.size > 2 * 1024 * 1024)) {
                e.preventDefault();

                const progressSection = document.getElementById('progress-section');
                const progressBar = document.getElementById('progress-bar');
                const submitBtn = document.getElementById('submit-btn');

                if (progressSection) progressSection.style.display = 'block';
                if (submitBtn) submitBtn.disabled = true;

                const chunkSize = 512 * 1024; // 512KB chunk size (fits under default Nginx 1MB client_max_body_size)
                const totalChunks = Math.ceil(file.size / chunkSize);
                const uuid = 'vid_' + Date.now() + '_' + Math.random().toString(36).substring(2, 9);
                const csrfToken = document.querySelector('input[name="_token"]').value;

                let tempFilePath = null;
                let tempFileName = null;

                try {
                    for (let i = 0; i < totalChunks; i++) {
                        const start = i * chunkSize;
                        const end = Math.min(file.size, start + chunkSize);
                        const chunk = file.slice(start, end);

                        const formData = new FormData();
                        formData.append('_token', csrfToken);
                        formData.append('file_uuid', uuid);
                        formData.append('chunk_index', i);
                        formData.append('total_chunks', totalChunks);
                        formData.append('file_name', file.name);
                        formData.append('file_chunk', chunk, file.name);

                        const response = await fetch('{{ route("dashboard.resources.upload_chunk") }}', {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });

                        if (!response.ok) {
                            throw new Error('Server returned HTTP ' + response.status);
                        }

                        const resData = await response.json();
                        if (!resData.success) {
                            throw new Error(resData.message || 'Chunk upload failed');
                        }

                        const pct = Math.round(((i + 1) / totalChunks) * 100);
                        if (progressBar) progressBar.style.width = pct + '%';

                        if (resData.temp_file_path) {
                            tempFilePath = resData.temp_file_path;
                            tempFileName = resData.original_name;
                        }
                    }

                    // Append temp file info to form
                    let tempPathInput = fullForm.querySelector('input[name="temp_file_path"]');
                    if (!tempPathInput) {
                        tempPathInput = document.createElement('input');
                        tempPathInput.type = 'hidden';
                        tempPathInput.name = 'temp_file_path';
                        fullForm.appendChild(tempPathInput);
                    }
                    tempPathInput.value = tempFilePath;

                    let tempNameInput = fullForm.querySelector('input[name="temp_file_name"]');
                    if (!tempNameInput) {
                        tempNameInput = document.createElement('input');
                        tempNameInput.type = 'hidden';
                        tempNameInput.name = 'temp_file_name';
                        fullForm.appendChild(tempNameInput);
                    }
                    tempNameInput.value = tempFileName;

                    fileInput.removeAttribute('required');
                    fileInput.removeAttribute('name');

                    fullForm.submit();

                } catch (err) {
                    alert('Upload error: ' + err.message + '. Please try again or check server upload settings.');
                    if (progressSection) progressSection.style.display = 'none';
                    if (submitBtn) submitBtn.disabled = false;
                }
            }
        });
    }

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
