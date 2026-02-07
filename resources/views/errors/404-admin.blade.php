<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ __('common.page_not_found_title') }} | {{ config('app.name') }}</title>
    <link rel="icon" href="{{ getImageOrPlaceholder(setting('app_favicon'), '32x32') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; background: #f8fafc; min-height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; }
        .error-404-admin { max-width: 440px; width: 100%; }
        .error-404-admin .card { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); }
        .error-404-admin .code { font-size: 4rem; font-weight: 800; color: #e2e8f0; line-height: 1; }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-404-admin mx-auto text-center px-3">
            <div class="card py-5 px-4">
                <div class="code mb-2" aria-hidden="true">404</div>
                <div class="mb-3">
                    <i class="fas fa-exclamation-triangle text-warning" style="font-size: 2.5rem;" aria-hidden="true"></i>
                </div>
                <h1 class="h5 fw-bold text-dark mb-2">{{ __('common.page_not_found_title') }}</h1>
                <p class="text-muted small mb-4">{{ __('common.page_not_found_message') }}</p>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-th-large me-2" aria-hidden="true"></i>{{ __('common.back_to_dashboard') }}
                </a>
            </div>
        </div>
    </div>
</body>
</html>
