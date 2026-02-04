@extends('layouts.admin')

@section('page_title', __('Email Configuration'))

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">{{ __('Email Configuration') }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Email Configuration') }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.email-configuration.templates') }}" class="btn btn-outline-primary">
                <i class="fas fa-envelope-open-text me-2"></i> {{ __('Manage Templates') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ __('Email Configuration') }}</h5>
        </div>
        <div class="card-body">
            
            <form action="{{ route('admin.email-configuration.update') }}" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" name="mail_mailer" value="smtp">

                <div class="mb-3">
                    <label for="mail_from_name" class="form-label">{{ __('Sender Name') }}</label>
                    <input type="text" class="form-control" id="mail_from_name" name="mail_from_name" value="{{ $settings['mail_from_name'] ?? config('mail.from.name') }}">
                </div>

                <div class="mb-3">
                    <label for="mail_host" class="form-label">{{ __('Mail Host') }}</label>
                    <input type="text" class="form-control" id="mail_host" name="mail_host" value="{{ $settings['mail_host'] ?? config('mail.mailers.smtp.host') }}">
                </div>

                <div class="mb-3">
                    <label for="mail_from_address" class="form-label">{{ __('Email') }}</label>
                    <input type="email" class="form-control" id="mail_from_address" name="mail_from_address" value="{{ $settings['mail_from_address'] ?? config('mail.from.address') }}">
                </div>

                <div class="mb-3">
                    <label for="mail_username" class="form-label">{{ __('SMTP User Name') }}</label>
                    <input type="text" class="form-control" id="mail_username" name="mail_username" value="{{ $settings['mail_username'] ?? config('mail.mailers.smtp.username') }}">
                </div>

                <div class="mb-3">
                    <label for="mail_password" class="form-label">{{ __('SMTP Password') }}</label>
                    <input type="password" class="form-control" id="mail_password" name="mail_password" value="{{ $settings['mail_password'] ?? config('mail.mailers.smtp.password') }}">
                </div>

                <div class="mb-3">
                    <label for="mail_port" class="form-label">{{ __('Mail Port') }}</label>
                    <input type="number" class="form-control" id="mail_port" name="mail_port" value="{{ $settings['mail_port'] ?? config('mail.mailers.smtp.port') }}">
                </div>

                <div class="mb-3">
                    <label for="mail_encryption" class="form-label">{{ __('Mail Encryption') }}</label>
                    <select class="form-select" id="mail_encryption" name="mail_encryption">
                        <option value="tls" {{ ($settings['mail_encryption'] ?? config('mail.mailers.smtp.encryption')) == 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ ($settings['mail_encryption'] ?? config('mail.mailers.smtp.encryption')) == 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value="null" {{ ($settings['mail_encryption'] ?? config('mail.mailers.smtp.encryption')) == 'null' ? 'selected' : '' }}>{{ __('None') }}</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
