document.addEventListener('DOMContentLoaded', () => {

    // Scroll Header
    const navbar = document.querySelector('.navbar');
    window.addEventListener('scroll', () => {
        navbar?.classList.toggle('scrolled', window.scrollY > 100);
    });

    // Scroll to Top
    const scrollBtn = document.querySelector('.scroll-top');
    window.addEventListener('scroll', () => {
        scrollBtn?.classList.toggle('visible', window.scrollY > 500);
    });

    scrollBtn?.addEventListener('click', e => {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Categories Dropdown Toggle
    const categoryToggle = document.getElementById('headerCategoriesToggle');
    const categoryDropdown = document.getElementById('headerCategoriesDropdown');

    if (categoryToggle && categoryDropdown) {
        categoryToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            categoryDropdown.classList.toggle('open');
            const isExpanded = categoryDropdown.classList.contains('open');
            categoryToggle.setAttribute('aria-expanded', isExpanded);
        });

        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (!categoryToggle.contains(e.target) && !categoryDropdown.contains(e.target)) {
                categoryDropdown.classList.remove('open');
                categoryToggle.setAttribute('aria-expanded', 'false');
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && categoryDropdown.classList.contains('open')) {
                categoryDropdown.classList.remove('open');
                categoryToggle.setAttribute('aria-expanded', 'false');
                categoryToggle.focus();
            }
        });
    }
});
