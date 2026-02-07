@extends('layouts.admin')

@section('page_title', __('Edit Page'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">{{ __('Edit Page') }}</h2>
        <p class="text-muted mb-0">{{ __('Update static page content.') }}</p>
    </div>
    <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> {{ __('Back to List') }}</a>
</div>

@php
    $localeLabels = $languages->pluck('name', 'code')->toArray();
    $hasMultipleLanguages = $languages->count() > 1;
@endphp

@if($hasMultipleLanguages)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h6 class="fw-bold mb-3">{{ __('common.switch_to_language_translation') }}</h6>
        <ul class="nav nav-tabs nav-tabs-translation flex-nowrap overflow-auto pb-1 mb-0" id="localeTabs" role="tablist">
            @foreach($languages as $lang)
                <li class="nav-item flex-shrink-0" role="presentation">
                    <button class="nav-link {{ $lang->code === $defaultLocale ? 'active' : '' }}" id="tab-{{ $lang->code }}" data-bs-toggle="tab" data-bs-target="#pane-{{ $lang->code }}" type="button" role="tab" data-locale="{{ $lang->code }}">
                        @if($lang->code === $defaultLocale)
                            <i class="bi bi-eye me-1"></i>
                        @else
                            <i class="bi bi-pencil me-1"></i>
                        @endif
                        {{ $lang->name }}
                    </button>
                </li>
            @endforeach
        </ul>
        <p class="mb-0 small text-muted mt-2" id="editingModeLabel">
            {{ __('common.your_editing_mode') }} <strong>{{ $defaultLanguage?->name ?? $defaultLocale }}</strong>
        </p>
    </div>
</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('admin.pages.update', $page) }}" method="POST" enctype="multipart/form-data" id="pageForm">
            @csrf
            @method('PUT')

            <div class="tab-content" id="localeTabContent">
                @foreach($languages as $lang)
                    @php
                        $locale = $lang->code;
                        $isDefault = $locale === $defaultLocale;
                        $trans = $page->getTranslationValuesForLocale($locale);
                    @endphp
                    <div class="tab-pane fade {{ $isDefault ? 'show active' : '' }}" id="pane-{{ $locale }}" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-lg-8">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ __('Page Title') }} <span class="text-danger">*</span></label>
                                    @if($isDefault)
                                        <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $page->title) }}" required>
                                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    @else
                                        <input type="text" class="form-control" name="translations[{{ $locale }}][title]" value="{{ old("translations.{$locale}.title", $trans['title'] ?? '') }}" placeholder="{{ __('common.optional_translation') }}">
                                    @endif
                                </div>

                                @if($isDefault)
                                    <div class="alert alert-info border-0 shadow-sm mb-3">
                                        <div class="d-flex">
                                            <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                                            <div>
                                                <h6 class="fw-bold mb-1">{{ __('Content Editor Guide') }}</h6>
                                                <p class="mb-0 small">{{ __('You can upload images directly in the editor! Click the Image icon > Upload tab, or drag and drop images. For the best result, use the Banner Image field on the right for the main page header.') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ __('Content') }}</label>
                                    @if($isDefault)
                                        <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="15">{{ old('content', $page->content) }}</textarea>
                                        @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    @else
                                        <textarea class="form-control rich-editor" id="content_{{ $locale }}" name="translations[{{ $locale }}][content]" rows="15" data-locale="{{ $locale }}">{{ old("translations.{$locale}.content", $trans['content'] ?? '') }}</textarea>
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-4">
                                @if($isDefault)
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">{{ __('Page Status') }}</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $page->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label for="image" class="form-label fw-bold">{{ __('Breadcrumb Image') }}</label>
                                        @if($page->image)
                                            <div class="mb-2">
                                                <img src="{{ filter_var($page->image, FILTER_VALIDATE_URL) ? $page->image : asset($page->image) }}" alt="" class="img-fluid rounded border">
                                            </div>
                                        @endif
                                        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
                                        <div class="form-text">{{ __('Recommended size: 1920x400px') }}</div>
                                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <hr>
                                    <h5 class="fw-bold mb-3">{{ __('SEO Settings') }}</h5>
                                @endif

                                <div class="mb-3">
                                    <label class="form-label">{{ __('Meta Title') }}</label>
                                    @if($isDefault)
                                        <input type="text" class="form-control @error('meta_title') is-invalid @enderror" name="meta_title" value="{{ old('meta_title', $page->meta_title) }}">
                                        @error('meta_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    @else
                                        <input type="text" class="form-control" name="translations[{{ $locale }}][meta_title]" value="{{ old("translations.{$locale}.meta_title", $trans['meta_title'] ?? '') }}">
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Meta Description') }}</label>
                                    @if($isDefault)
                                        <textarea class="form-control @error('meta_description') is-invalid @enderror" name="meta_description" rows="3">{{ old('meta_description', $page->meta_description) }}</textarea>
                                        @error('meta_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    @else
                                        <textarea class="form-control" name="translations[{{ $locale }}][meta_description]" rows="3">{{ old("translations.{$locale}.meta_description", $trans['meta_description'] ?? '') }}</textarea>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.pages.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i> {{ __('Update Page') }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
.nav-tabs-translation .nav-link { border: 1px solid #dee2e6; margin-right: 4px; border-radius: 6px; font-weight: 500; }
.nav-tabs-translation .nav-link.active { background: #e7f1ff; border-color: #0d6efd; color: #0d6efd; }
.nav-tabs-translation .nav-link:hover:not(.active) { background: #f8f9fa; }
@media (max-width: 576px) { .nav-tabs-translation { overflow-x: auto; -webkit-overflow-scrolling: touch; } .nav-tabs-translation .nav-link { font-size: 0.875rem; padding: 0.5rem 0.75rem; } }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var localeLabels = @json($localeLabels);
    var defaultLocale = @json($defaultLocale);

    document.querySelectorAll('#localeTabs [data-bs-toggle="tab"]').forEach(function(tab) {
        tab.addEventListener('shown.bs.tab', function() {
            var locale = this.getAttribute('data-locale');
            var label = (localeLabels && localeLabels[locale]) || locale;
            var el = document.getElementById('editingModeLabel');
            if (el) el.innerHTML = '{{ __("common.your_editing_mode") }} <strong>' + label + '</strong>';
        });
    });

    var uploadUrl = '{{ route("admin.pages.upload-image") }}';
    var csrf = '{{ csrf_token() }}';

    var baseConfig = {
        menubar: true,
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount code',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat | code',
        height: 400,
        images_upload_handler: function(blobInfo, progress) {
            return new Promise(function(resolve, reject) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', uploadUrl);
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
                xhr.upload.onprogress = function(e) { progress(e.loaded / e.total * 100); };
                xhr.onload = function() {
                    if (xhr.status === 403) { reject({ message: 'HTTP Error: ' + xhr.status, remove: true }); return; }
                    if (xhr.status < 200 || xhr.status >= 300) { reject('HTTP Error: ' + xhr.status); return; }
                    var json = JSON.parse(xhr.responseText);
                    if (!json || typeof json.location !== 'string') { reject('Invalid JSON'); return; }
                    resolve(json.location);
                };
                xhr.onerror = function() { reject('Upload failed'); };
                var fd = new FormData();
                fd.append('file', blobInfo.blob(), blobInfo.filename());
                xhr.send(fd);
            });
        },
        setup: function(editor) {
            editor.on('change', function() { editor.save(); });
        }
    };

    tinymce.init(Object.assign({ selector: '#content' }, baseConfig, { height: 500 }));

    document.querySelectorAll('.rich-editor').forEach(function(textarea) {
        if (!textarea.id) return;
        tinymce.init(Object.assign({ selector: '#' + textarea.id }, baseConfig));
    });
});
</script>
@endpush
@endsection
