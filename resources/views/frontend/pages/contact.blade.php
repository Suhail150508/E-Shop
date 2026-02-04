@extends('layouts.frontend')

@section('content')
@include('frontend.partials.breadcrumb', ['title' => $page->title ?? __('common.contact'), 'bgImage' => $page->image ?? 'https://images.unsplash.com/photo-1423666639041-f142fcb93461?ixlib=rb-1.2.1&auto=format&fit=crop&w=1951&q=80'])

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            @if(isset($page) && $page->content)
                {!! $page->content !!}
            @else
                <p class="text-center mb-5 lead">{{ __('common.contact_intro') }}</p>

                <div class="row g-4 mb-5">
                    <div class="col-md-4 text-center">
                        <div class="mb-3">
                            <i class="fas fa-map-marker-alt fa-2x text-terracotta"></i>
                        </div>
                        <h5>{{ __('common.visit_us') }}</h5>
                        <p class="text-muted">123 Fashion Street,<br>New York, NY 10001</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="mb-3">
                            <i class="fas fa-envelope fa-2x text-terracotta"></i>
                        </div>
                        <h5>{{ __('common.email_us') }}</h5>
                        <p class="text-muted">support@luxe-store.com<br>info@luxe-store.com</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="mb-3">
                            <i class="fas fa-phone fa-2x text-terracotta"></i>
                        </div>
                        <h5>{{ __('common.call_us') }}</h5>
                        <p class="text-muted">+1 (555) 123-4567<br>Mon-Fri, 9am-6pm</p>
                    </div>
                </div>
            @endif

            <div class="card border-0 shadow-sm mt-5">
                <div class="card-body p-4">
                    <h4 class="mb-4">{{ __('common.send_message') }}</h4>
                    <form id="contactForm" action="{{ route('contact.submit') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('common.name') }}</label>
                                <input type="text" name="name" class="form-control" placeholder="{{ __('common.your_name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('common.email') }}</label>
                                <input type="email" name="email" class="form-control" placeholder="{{ __('common.your_email') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('common.phone') }}</label>
                                <input type="text" name="phone" class="form-control" placeholder="{{ __('common.your_phone') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('common.subject') }}</label>
                                <input type="text" name="subject" class="form-control" placeholder="{{ __('common.how_can_we_help') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ __('common.message') }}</label>
                                <textarea name="message" class="form-control" rows="5" placeholder="{{ __('common.your_message') }}" required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-terracotta text-white" id="contactBtn">
                                    <span class="btn-text">{{ __('common.send_message_btn') }}</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .text-terracotta {
        color: #E07A5F !important;
    }
    .btn-terracotta {
        background-color: #E07A5F;
        border-color: #E07A5F;
    }
    .btn-terracotta:hover {
        background-color: #d16b50;
        border-color: #d16b50;
        color: white;
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const contactForm = document.getElementById('contactForm');
        if (contactForm) {
            contactForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const form = this;
                const btn = form.querySelector('#contactBtn');
                const btnText = btn.querySelector('.btn-text');
                const spinner = btn.querySelector('.spinner-border');
                
                // Reset errors
                const invalidInputs = form.querySelectorAll('.is-invalid');
                invalidInputs.forEach(input => input.classList.remove('is-invalid'));
                
                // Loading state
                btn.disabled = true;
                btnText.classList.add('d-none');
                spinner.classList.remove('d-none');
                
                const formData = new FormData(form);
                
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        toastr.success(data.message);
                        form.reset();
                    } else {
                        if (data.errors) {
                            // Handle validation errors
                            Object.keys(data.errors).forEach(key => {
                                const input = form.querySelector(`[name="${key}"]`);
                                if (input) {
                                    input.classList.add('is-invalid');
                                    toastr.error(data.errors[key][0]);
                                }
                            });
                        } else if (data.message) {
                            toastr.error(data.message);
                        } else {
                            toastr.error('Something went wrong. Please try again.');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('An error occurred. Please try again later.');
                })
                .finally(() => {
                    // Reset state
                    btn.disabled = false;
                    btnText.classList.remove('d-none');
                    spinner.classList.add('d-none');
                });
            });
        }
    });
</script>
@endpush
@endsection
