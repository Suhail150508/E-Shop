/**
 * Shop Page Scripts
 * Handles filters, view toggling, and responsive behavior
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize filters
    updateActiveFilters();
    handleResponsiveFilters();
    window.addEventListener('resize', handleResponsiveFilters);
    
    // Set initial view from localStorage
    const savedView = localStorage.getItem('shopView') || 'grid';
    setView(savedView, false);
    
    // Initialize all filter sections
    document.querySelectorAll('.filter-title').forEach((title, index) => {
        const content = title.nextElementSibling;
        
        // Keep the first section (Category) open by default, collapse others
        if (index === 0) {
            content.style.maxHeight = content.scrollHeight + 'px';
            title.classList.remove('collapsed');
        } else {
            content.style.maxHeight = '0px';
            title.classList.add('collapsed');
        }
        
        content.style.overflow = 'hidden';
        // Use a slight timeout to ensure transitions work after load
        setTimeout(() => {
            content.style.transition = 'max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
        }, 100);
    });

    // Mobile filter toggle listeners
    const mobileToggle = document.getElementById('mobileFilterToggle');
    if(mobileToggle) mobileToggle.addEventListener('click', openMobileFilters);
    
    const filterOverlay = document.getElementById('filterOverlay');
    if(filterOverlay) filterOverlay.addEventListener('click', closeMobileFilters);

    // Price Range Logic
    const priceRange = document.getElementById('priceRange');
    const maxPriceInput = document.getElementById('maxPriceInput');
    const priceOutput = document.getElementById('priceOutput');

    if (priceRange && maxPriceInput) {
        // Slider updates input and output
        priceRange.addEventListener('input', function() {
            maxPriceInput.value = this.value;
            if(priceOutput) priceOutput.textContent = this.value;
        });

        // Input updates slider and output
        maxPriceInput.addEventListener('input', function() {
            let val = parseInt(this.value);
            if (val > 1000) val = 1000; // Max limit
            // if (val < 0) val = 0; // Min limit (optional)
            
            priceRange.value = val;
            if(priceOutput) priceOutput.textContent = val;
        });
    }
});

// Toggle filter sections
function toggleFilter(element) {
    const content = element.nextElementSibling;
    
    // Toggle class
    element.classList.toggle('collapsed');
    
    // Update height based on class state
    if (element.classList.contains('collapsed')) {
        content.style.maxHeight = '0px';
    } else {
        content.style.maxHeight = content.scrollHeight + 'px';
    }
}

function toggleColor(element) {
    element.classList.toggle('active');
    updateColorInput();
    updateActiveFilters();
}

function updateColorInput() {
    const colors = Array.from(document.querySelectorAll('.color-option.active'))
        .map(opt => opt.dataset.color);
    document.getElementById('colorInput').value = colors.join(',');
}

// Set grid/list view
function setView(view, save = true) {
    const grid = document.getElementById('productsGrid');
    const buttons = document.querySelectorAll('.view-btn');
    
    buttons.forEach(btn => btn.classList.remove('active'));
    // Find the button for this view
    const activeBtn = document.querySelector(`.view-btn[onclick="setView('${view}')"]`);
    if (activeBtn) activeBtn.classList.add('active');
    
    if (view === 'list') {
        grid.classList.add('list-view');
    } else {
        grid.classList.remove('list-view');
    }
    
    if (save) {
        localStorage.setItem('shopView', view);
    }
}

// Mobile filters
function handleResponsiveFilters() {
    const sidebar = document.getElementById('desktopFilters');
    const mobileContainer = document.getElementById('mobileFiltersContent');
    const content = document.querySelector('.filter-card');
    
    // Safety check
    if (!content || !sidebar || !mobileContainer) return;
    
    if (window.innerWidth <= 900) {
        // Move to mobile if not already there
        if (!mobileContainer.contains(content)) {
            mobileContainer.appendChild(content);
        }
    } else {
        // Move to desktop if not already there
        if (!sidebar.contains(content)) {
            sidebar.appendChild(content);
        }
    }
}

function openMobileFilters() {
    const mobileFilters = document.getElementById('mobileFilters');
    const filterOverlay = document.getElementById('filterOverlay');
    if (mobileFilters) mobileFilters.classList.add('show');
    if (filterOverlay) filterOverlay.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeMobileFilters() {
    const mobileFilters = document.getElementById('mobileFilters');
    const filterOverlay = document.getElementById('filterOverlay');
    if (mobileFilters) mobileFilters.classList.remove('show');
    if (filterOverlay) filterOverlay.classList.remove('show');
    document.body.style.overflow = '';
}

// Clear all filters
function clearAllFilters() {
    // Uncheck all checkboxes
    document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
    
    // Remove active color options
    document.querySelectorAll('.color-option.active').forEach(opt => opt.classList.remove('active'));
    
    // Clear price inputs
    document.querySelectorAll('input[name="min_price"], input[name="max_price"]').forEach(input => {
        input.value = '';
    });
    
    // Update color input
    updateColorInput();
    
    // Update active filters display
    updateActiveFilters();
    
    // Submit form
    document.getElementById('filtersForm').submit();
}

// Update active filters display
function updateActiveFilters() {
    const container = document.getElementById('activeFilters');
    const form = document.getElementById('filtersForm');
    if(!form || !container) return;
    
    const formData = new FormData(form);
    const transCommon = window.translations?.common || {};
    
    let activeFilters = [];
    
    // Check categories
    const categories = formData.getAll('categories[]');
    if (categories.length) {
        categories.forEach(id => {
            const label = document.querySelector(`input[name="categories[]"][value="${id}"]`)
                ?.closest('.filter-label')?.querySelector('span:first-child')?.textContent || `Category ${id}`;
            activeFilters.push({
                label: label,
                remove: () => {
                    const checkbox = document.querySelector(`input[name="categories[]"][value="${id}"]`);
                    if (checkbox) checkbox.checked = false;
                    form.submit();
                }
            });
        });
    }
    
    // Check price range
    const minPrice = formData.get('min_price');
    const maxPrice = formData.get('max_price');
    if (minPrice || maxPrice) {
        let label = '';
        const fromTxt = transCommon.from_price || 'From';
        const upToTxt = transCommon.up_to_price || 'Up to';
        
        if (minPrice && maxPrice) {
            label = `$${minPrice} - $${maxPrice}`;
        } else if (minPrice) {
            label = `${fromTxt} $${minPrice}`;
        } else {
            label = `${upToTxt} $${maxPrice}`;
        }

        activeFilters.push({
            label: label,
            remove: () => {
                document.querySelector('input[name="min_price"]').value = '';
                document.querySelector('input[name="max_price"]').value = '';
                form.submit();
            }
        });
    }
    
    // Check colors
    const colors = formData.get('colors')?.split(',').filter(c => c) || [];
    if (colors.length) {
        colors.forEach(color => {
            activeFilters.push({
                label: color.charAt(0).toUpperCase() + color.slice(1),
                remove: () => {
                    const colorOption = document.querySelector(`.color-option[data-color="${color}"]`);
                    if (colorOption) colorOption.classList.remove('active');
                    updateColorInput();
                    form.submit();
                }
            });
        });
    }
    
    // Check availability
    const availability = formData.getAll('availability[]');
    if (availability.length) {
        availability.forEach(value => {
            const label = value.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
            activeFilters.push({
                label: label,
                remove: () => {
                    const checkbox = document.querySelector(`input[name="availability[]"][value="${value}"]`);
                    if (checkbox) checkbox.checked = false;
                    form.submit();
                }
            });
        });
    }
    
    // Update display
    container.innerHTML = '';
    if (activeFilters.length > 0) {
        activeFilters.forEach(filter => {
            const tag = document.createElement('div');
            tag.className = 'filter-tag';
            tag.innerHTML = `${filter.label} <i class="fas fa-times"></i>`;
            tag.addEventListener('click', filter.remove);
            container.appendChild(tag);
        });
        container.style.display = 'flex';
    } else {
        container.style.display = 'none';
    }
}
