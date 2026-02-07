@extends('layouts.admin')

@section('page_title', __('Edit Email Template'))

@push('styles')
<link href="{{ asset('backend/vendor/summernote/summernote-lite.min.css') }}" rel="stylesheet">
<style>
    /*
    .note-editor .note-toolbar {
        background-color: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    .note-editor.note-frame {
        border-color: #e2e8f0;
        border-radius: 0.5rem;
    }
    */
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">{{ __('Edit Email Template') }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.email-configuration.index') }}" class="text-decoration-none">{{ __('Email Configuration') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.email-configuration.templates') }}" class="text-decoration-none">{{ __('Templates') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Edit') }}: {{ $template->name }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.email-configuration.templates') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> {{ __('Back to Templates') }}
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Template Details') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.email-configuration.templates.update', $template->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Template Name') }}</label>
                            <input type="text" class="form-control" value="{{ $template->name }}" disabled>
                            <div class="form-text">{{ __('System name, cannot be changed.') }}</div>
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">{{ __('Subject') }}</label>
                            <input type="text" class="form-control" id="subject" name="subject" value="{{ old('subject', $template->subject) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">{{ __('Message Content') }}</label>
                            <textarea class="form-control" id="message" name="message" rows="10">{{ old('message', $template->message) }}</textarea>
                            <div class="form-text">{{ __('Available variables:') }} <code>{{ '{' . '{app_name}' . '}' }}</code>, <code>{{ '{' . '{name}' . '}' }}</code></div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> {{ __('Update Template') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Available Variables') }}</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">{{ __('You can use these variables in the subject and message body.') }}</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <code>{{ '{' . '{app_name}' . '}' }}</code>
                            <span class="text-muted small">{{ __('Application Name') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <code>{{ '{' . '{name}' . '}' }}</code>
                            <span class="text-muted small">{{ __('User Name') }}</span>
                        </li>
                        @if($template->slug == 'order_confirmation')
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <code>{{ '{' . '{order_number}' . '}' }}</code>
                            <span class="text-muted small">{{ __('Order Number') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <code>{{ '{' . '{total_amount}' . '}' }}</code>
                            <span class="text-muted small">{{ __('Order Total') }}</span>
                        </li>
                        @endif
                        @if($template->slug == 'reset_password')
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <code>{{ '{' . '{reset_link}' . '}' }}</code>
                            <span class="text-muted small">{{ __('Password Reset Link') }}</span>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('backend/vendor/summernote/summernote-lite.min.js') }}"></script>
<script>
    $(document).ready(function() {
        if (typeof $.fn.summernote !== 'undefined') {
            $('#message').summernote({
                placeholder: '{{ __('Write your email content here...') }}',
                tabsize: 2,
                height: 300,
                toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        }
    });
</script>
@endpush
