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
