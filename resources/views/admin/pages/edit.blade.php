@extends('layouts.admin')

@section('page_title', __('Edit Page'))

@section('content')

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
                        @php $isAboutPage = $page->slug === 'about-us'; @endphp
                        <div class="row g-4">
                            <div class="{{ $isAboutPage ? 'col-lg-4' : 'col-lg-8' }}">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ __('Page Title') }} <span class="text-danger">*</span></label>
                                    @if($isDefault)
                                        <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $page->title) }}" required>
                                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    @else
                                        <input type="text" class="form-control" name="translations[{{ $locale }}][title]" value="{{ old("translations.{$locale}.title", $trans['title'] ?? '') }}" placeholder="{{ __('common.optional_translation') }}">
                                    @endif
                                </div>

                                @if(!$isAboutPage)
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ __('Content') }}</label>
                                    @if($isDefault)
                                        <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="15">{{ old('content', $page->content) }}</textarea>
                                        @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    @else
                                        <textarea class="form-control rich-editor" id="content_{{ $locale }}" name="translations[{{ $locale }}][content]" rows="15" data-locale="{{ $locale }}">{{ old("translations.{$locale}.content", $trans['content'] ?? '') }}</textarea>
                                    @endif
                                </div>
                                @endif
                            </div>

                            <div class="{{ $isAboutPage ? 'col-lg-8' : 'col-lg-4' }}">
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
                        @if($page->slug === 'about-us')
                        <hr class="my-4">
                        <h5 class="mb-3 text-primary">{{ __('common.about_hero_section') }} @if(!$isDefault)<span class="badge bg-secondary">{{ $lang->name }}</span>@endif</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Title') }}</label>
                                @if($isDefault)
                                    <input type="text" class="form-control" name="about_hero_title" value="{{ old('about_hero_title', $page->meta['about_hero_title'] ?? '') }}" placeholder="About {{ config('app.name') }}">
                                @else
                                    <input type="text" class="form-control" name="translations[{{ $locale }}][about_hero_title]" value="{{ old("translations.{$locale}.about_hero_title", $trans['about_hero_title'] ?? '') }}" placeholder="About {{ config('app.name') }}">
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Subtitle') }}</label>
                                @if($isDefault)
                                    <input type="text" class="form-control" name="about_hero_subtitle" value="{{ old('about_hero_subtitle', $page->meta['about_hero_subtitle'] ?? '') }}">
                                @else
                                    <input type="text" class="form-control" name="translations[{{ $locale }}][about_hero_subtitle]" value="{{ old("translations.{$locale}.about_hero_subtitle", $trans['about_hero_subtitle'] ?? '') }}">
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Description') }}</label>
                                @if($isDefault)
                                    <textarea class="form-control" name="about_hero_text" rows="3">{{ old('about_hero_text', $page->meta['about_hero_text'] ?? '') }}</textarea>
                                @else
                                    <textarea class="form-control" name="translations[{{ $locale }}][about_hero_text]" rows="3">{{ old("translations.{$locale}.about_hero_text", $trans['about_hero_text'] ?? '') }}</textarea>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Hero Image') }}</label>
                                @php $heroImg = $isDefault ? ($page->meta['about_hero_image'] ?? null) : ($trans['about_hero_image'] ?? null); @endphp
                                @if(!empty($heroImg))
                                    <div class="mb-2"><img src="{{ getImageOrPlaceholder($heroImg, '400x200') }}" height="60" class="rounded object-fit-cover"></div>
                                @endif
                                @if($isDefault)
                                    <input type="file" class="form-control" name="about_hero_image">
                                @else
                                    <input type="file" class="form-control" name="translations[{{ $locale }}][about_hero_image]">
                                @endif
                            </div>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label class="form-label">{{ __('common.about_story_section') }}</label>
                            </div>
                            <div class="col-md-12">
                                @if($isDefault)
                                    <input type="text" class="form-control" name="about_story_title" value="{{ old('about_story_title', $page->meta['about_story_title'] ?? '') }}" placeholder="Our Story">
                                @else
                                    <input type="text" class="form-control" name="translations[{{ $locale }}][about_story_title]" value="{{ old("translations.{$locale}.about_story_title", $trans['about_story_title'] ?? '') }}" placeholder="Our Story">
                                @endif
                            </div>
                            <div class="col-md-12">
                                @if($isDefault)
                                    <input type="text" class="form-control" name="about_story_subtitle" value="{{ old('about_story_subtitle', $page->meta['about_story_subtitle'] ?? '') }}">
                                @else
                                    <input type="text" class="form-control" name="translations[{{ $locale }}][about_story_subtitle]" value="{{ old("translations.{$locale}.about_story_subtitle", $trans['about_story_subtitle'] ?? '') }}">
                                @endif
                            </div>
                            @foreach([1, 2, 3] as $i)
                            <div class="col-md-4">
                                @php
                                    $si = 'about_story_'.$i.'_image';
                                    $sh = 'about_story_'.$i.'_heading';
                                    $st = 'about_story_'.$i.'_text';
                                    $storyImg = $isDefault ? ($page->meta[$si] ?? null) : ($trans[$si] ?? null);
                                @endphp
                                <label class="form-label">{{ __('Block') }} {{ $i }} {{ __('Image') }}</label>
                                @if(!empty($storyImg))<div class="mb-2"><img src="{{ getImageOrPlaceholder($storyImg, '300x200') }}" height="50" class="rounded object-fit-cover"></div>@endif
                                @if($isDefault)
                                    <input type="file" class="form-control" name="about_story_{{ $i }}_image">
                                    <input type="text" class="form-control mt-2" name="about_story_{{ $i }}_heading" value="{{ old($sh, $page->meta[$sh] ?? '') }}" placeholder="{{ __('Heading') }}">
                                    <textarea class="form-control mt-2" name="about_story_{{ $i }}_text" rows="2">{{ old($st, $page->meta[$st] ?? '') }}</textarea>
                                @else
                                    <input type="file" class="form-control" name="translations[{{ $locale }}][about_story_{{ $i }}_image]">
                                    <input type="text" class="form-control mt-2" name="translations[{{ $locale }}][about_story_{{ $i }}_heading]" value="{{ old("translations.{$locale}.{$sh}", $trans[$sh] ?? '') }}" placeholder="{{ __('Heading') }}">
                                    <textarea class="form-control mt-2" name="translations[{{ $locale }}][about_story_{{ $i }}_text]" rows="2">{{ old("translations.{$locale}.{$st}", $trans[$st] ?? '') }}</textarea>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label class="form-label">{{ __('common.about_mission_section') }}</label>
                            </div>
                            <div class="col-md-12">
                                @if($isDefault)
                                    <input type="text" class="form-control" name="about_mission_title" value="{{ old('about_mission_title', $page->meta['about_mission_title'] ?? '') }}" placeholder="Our Mission & Vision">
                                @else
                                    <input type="text" class="form-control" name="translations[{{ $locale }}][about_mission_title]" value="{{ old("translations.{$locale}.about_mission_title", $trans['about_mission_title'] ?? '') }}">
                                @endif
                            </div>
                            <div class="col-md-12">
                                @if($isDefault)
                                    <textarea class="form-control" name="about_mission_intro" rows="2">{{ old('about_mission_intro', $page->meta['about_mission_intro'] ?? '') }}</textarea>
                                @else
                                    <textarea class="form-control" name="translations[{{ $locale }}][about_mission_intro]" rows="2">{{ old("translations.{$locale}.about_mission_intro", $trans['about_mission_intro'] ?? '') }}</textarea>
                                @endif
                            </div>
                            <div class="col-md-6">
                                @php $m1i = $isDefault ? ($page->meta['about_mission_1_image'] ?? null) : ($trans['about_mission_1_image'] ?? null); @endphp
                                <label class="form-label">{{ __('Block') }} 1 {{ __('Image') }}</label>
                                @if(!empty($m1i))<div class="mb-2"><img src="{{ getImageOrPlaceholder($m1i, '400x300') }}" height="60" class="rounded object-fit-cover"></div>@endif
                                @if($isDefault)
                                    <input type="file" class="form-control" name="about_mission_1_image">
                                    <textarea class="form-control mt-2" name="about_mission_1_text" rows="3">{{ old('about_mission_1_text', $page->meta['about_mission_1_text'] ?? '') }}</textarea>
                                @else
                                    <input type="file" class="form-control" name="translations[{{ $locale }}][about_mission_1_image]">
                                    <textarea class="form-control mt-2" name="translations[{{ $locale }}][about_mission_1_text]" rows="3">{{ old("translations.{$locale}.about_mission_1_text", $trans['about_mission_1_text'] ?? '') }}</textarea>
                                @endif
                            </div>
                            <div class="col-md-6">
                                @php $m2i = $isDefault ? ($page->meta['about_mission_2_image'] ?? null) : ($trans['about_mission_2_image'] ?? null); @endphp
                                <label class="form-label">{{ __('Block') }} 2 {{ __('Image') }}</label>
                                @if(!empty($m2i))<div class="mb-2"><img src="{{ getImageOrPlaceholder($m2i, '400x300') }}" height="60" class="rounded object-fit-cover"></div>@endif
                                @if($isDefault)
                                    <input type="file" class="form-control" name="about_mission_2_image">
                                    <textarea class="form-control mt-2" name="about_mission_2_text" rows="3">{{ old('about_mission_2_text', $page->meta['about_mission_2_text'] ?? '') }}</textarea>
                                @else
                                    <input type="file" class="form-control" name="translations[{{ $locale }}][about_mission_2_image]">
                                    <textarea class="form-control mt-2" name="translations[{{ $locale }}][about_mission_2_text]" rows="3">{{ old("translations.{$locale}.about_mission_2_text", $trans['about_mission_2_text'] ?? '') }}</textarea>
                                @endif
                            </div>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label class="form-label">{{ __('common.about_testimonial_section') }}</label>
                            </div>
                            <div class="col-md-12">
                                @if($isDefault)
                                    <input type="text" class="form-control mb-2" name="about_testimonial_title" value="{{ old('about_testimonial_title', $page->meta['about_testimonial_title'] ?? '') }}" placeholder="Testimonials & Success Stories">
                                    <input type="text" class="form-control" name="about_testimonial_subtitle" value="{{ old('about_testimonial_subtitle', $page->meta['about_testimonial_subtitle'] ?? '') }}">
                                @else
                                    <input type="text" class="form-control mb-2" name="translations[{{ $locale }}][about_testimonial_title]" value="{{ old("translations.{$locale}.about_testimonial_title", $trans['about_testimonial_title'] ?? '') }}">
                                    <input type="text" class="form-control" name="translations[{{ $locale }}][about_testimonial_subtitle]" value="{{ old("translations.{$locale}.about_testimonial_subtitle", $trans['about_testimonial_subtitle'] ?? '') }}">
                                @endif
                            </div>
                            <div class="col-md-6">
                                @php $t1a = $isDefault ? ($page->meta['about_testimonial_1_avatar'] ?? null) : ($trans['about_testimonial_1_avatar'] ?? null); @endphp
                                <label class="form-label">{{ __('Testimonial') }} 1 {{ __('Avatar') }}</label>
                                @if(!empty($t1a))<div class="mb-2"><img src="{{ getImageOrPlaceholder($t1a, '80x80') }}" width="50" height="50" class="rounded-circle object-fit-cover"></div>@endif
                                @if($isDefault)
                                    <input type="file" class="form-control" name="about_testimonial_1_avatar">
                                    <input type="text" class="form-control mt-2" name="about_testimonial_1_name" value="{{ old('about_testimonial_1_name', $page->meta['about_testimonial_1_name'] ?? '') }}" placeholder="Name">
                                    <input type="text" class="form-control mt-2" name="about_testimonial_1_role" value="{{ old('about_testimonial_1_role', $page->meta['about_testimonial_1_role'] ?? '') }}" placeholder="Role">
                                    <textarea class="form-control mt-2" name="about_testimonial_1_quote" rows="2">{{ old('about_testimonial_1_quote', $page->meta['about_testimonial_1_quote'] ?? '') }}</textarea>
                                @else
                                    <input type="file" class="form-control" name="translations[{{ $locale }}][about_testimonial_1_avatar]">
                                    <input type="text" class="form-control mt-2" name="translations[{{ $locale }}][about_testimonial_1_name]" value="{{ old("translations.{$locale}.about_testimonial_1_name", $trans['about_testimonial_1_name'] ?? '') }}">
                                    <input type="text" class="form-control mt-2" name="translations[{{ $locale }}][about_testimonial_1_role]" value="{{ old("translations.{$locale}.about_testimonial_1_role", $trans['about_testimonial_1_role'] ?? '') }}">
                                    <textarea class="form-control mt-2" name="translations[{{ $locale }}][about_testimonial_1_quote]" rows="2">{{ old("translations.{$locale}.about_testimonial_1_quote", $trans['about_testimonial_1_quote'] ?? '') }}</textarea>
                                @endif
                            </div>
                            <div class="col-md-6">
                                @php $t2a = $isDefault ? ($page->meta['about_testimonial_2_avatar'] ?? null) : ($trans['about_testimonial_2_avatar'] ?? null); @endphp
                                <label class="form-label">{{ __('Testimonial') }} 2 {{ __('Avatar') }}</label>
                                @if(!empty($t2a))<div class="mb-2"><img src="{{ getImageOrPlaceholder($t2a, '80x80') }}" width="50" height="50" class="rounded-circle object-fit-cover"></div>@endif
                                @if($isDefault)
                                    <input type="file" class="form-control" name="about_testimonial_2_avatar">
                                    <input type="text" class="form-control mt-2" name="about_testimonial_2_name" value="{{ old('about_testimonial_2_name', $page->meta['about_testimonial_2_name'] ?? '') }}">
                                    <input type="text" class="form-control mt-2" name="about_testimonial_2_role" value="{{ old('about_testimonial_2_role', $page->meta['about_testimonial_2_role'] ?? '') }}">
                                    <textarea class="form-control mt-2" name="about_testimonial_2_quote" rows="2">{{ old('about_testimonial_2_quote', $page->meta['about_testimonial_2_quote'] ?? '') }}</textarea>
                                @else
                                    <input type="file" class="form-control" name="translations[{{ $locale }}][about_testimonial_2_avatar]">
                                    <input type="text" class="form-control mt-2" name="translations[{{ $locale }}][about_testimonial_2_name]" value="{{ old("translations.{$locale}.about_testimonial_2_name", $trans['about_testimonial_2_name'] ?? '') }}">
                                    <input type="text" class="form-control mt-2" name="translations[{{ $locale }}][about_testimonial_2_role]" value="{{ old("translations.{$locale}.about_testimonial_2_role", $trans['about_testimonial_2_role'] ?? '') }}">
                                    <textarea class="form-control mt-2" name="translations[{{ $locale }}][about_testimonial_2_quote]" rows="2">{{ old("translations.{$locale}.about_testimonial_2_quote", $trans['about_testimonial_2_quote'] ?? '') }}</textarea>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i> {{ __('Update Page') }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="{{ asset('backend/vendor/tinymce/tinymce.min.js') }}"></script>
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

    if (typeof tinymce !== 'undefined') {
        tinymce.init(Object.assign({ selector: '#content' }, baseConfig, { height: 500 }));

        document.querySelectorAll('.rich-editor').forEach(function(textarea) {
            if (!textarea.id) return;
            tinymce.init(Object.assign({ selector: '#' + textarea.id }, baseConfig));
        });
    }
});
</script>
@endpush
@endsection
