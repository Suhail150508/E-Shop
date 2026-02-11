/**
 * Newsletter Subscription Script
 */
document.addEventListener('DOMContentLoaded', function() {
    const newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const btn = form.querySelector('#newsletterBtn');
            const btnText = btn.querySelector('.btn-text');
            const loader = btn.querySelector('.loader-inline');
            const emailInput = form.querySelector('input[name="email"]');
            
            // Reset errors
            const existingError = form.querySelector('.invalid-feedback');
            if (existingError) existingError.remove();
            emailInput.classList.remove('is-invalid');
            
            // Loading state
            btn.disabled = true;
            btn.classList.add('btn-loading');
            btnText.classList.add('d-none');
            if (loader) loader.classList.remove('d-none');
            
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
                    if (window.toastr) window.toastr.success(data.message);
                    else alert(data.message);
                    form.reset();
                } else {
                    if (data.message) {
                        if (window.toastr) window.toastr.error(data.message);
                        else alert(data.message);
                        emailInput.classList.add('is-invalid');
                    } else {
                        const msg = window.translations?.common?.error_try_again || 'Something went wrong. Please try again.';
                        if (window.toastr) window.toastr.error(msg);
                        else alert(msg);
                    }
                }
            })
            .catch(error => {
                const msg = window.translations?.common?.error_occurred || 'An error occurred. Please try again later.';
                if (window.toastr) window.toastr.error(msg);
                else alert(msg);
            })
            .finally(() => {
                // Reset state
                btn.disabled = false;
                btn.classList.remove('btn-loading');
                btnText.classList.remove('d-none');
                if (loader) loader.classList.add('d-none');
            });
        });
    }
});
