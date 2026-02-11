/**
 * Shop Page Scripts
 * Handles filters, view toggling, and responsive behavior
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize filters
    // updateActiveFilters(); // Disabled to prevent conflict with server-side rendering
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
    // updateActiveFilters();
    document.getElementById('filtersForm').submit();
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

// Active Filters - Disabled for server-side rendering
/*
function updateActiveFilters() {
    const container = document.getElementById('activeFilters');
    if (!container) return;
    
    container.innerHTML = '';
    let hasFilters = false;

    // Categories
    const checkedCategories = document.querySelectorAll('input[name="categories[]"]:checked');
    checkedCategories.forEach(input => {
        hasFilters = true;
        const label = input.closest('.filter-option').querySelector('.filter-label span').textContent;
        addFilterTag(label, () => {
            input.checked = false;
            document.getElementById('filtersForm').submit();
        });
    });

    // Colors
    const activeColors = document.querySelectorAll('.color-option.active');
    activeColors.forEach(color => {
        hasFilters = true;
        const colorName = color.dataset.color;
        addFilterTag(colorName.charAt(0).toUpperCase() + colorName.slice(1), () => {
            color.classList.remove('active');
            updateColorInput();
            document.getElementById('filtersForm').submit();
        });
    });

    // Price
    const minPrice = document.getElementById('minPriceInput').value;
    const maxPrice = document.getElementById('maxPriceInput').value;
    if (minPrice > 0 || maxPrice < 1000) {
        hasFilters = true;
        addFilterTag(`$${minPrice} - $${maxPrice}`, () => {
            document.getElementById('minPriceInput').value = 0;
            document.getElementById('maxPriceInput').value = 1000;
            document.getElementById('filtersForm').submit();
        });
    }

    if (hasFilters) {
        container.style.display = 'flex';
        // Add Clear All button
        const clearAll = document.createElement('button');
        clearAll.className = 'clear-filters ms-auto';
        clearAll.innerHTML = '<i class="fas fa-times"></i> Clear All';
        clearAll.onclick = clearAllFilters;
        container.appendChild(clearAll);
    } else {
        container.style.display = 'none';
    }
}

function addFilterTag(text, onRemove) {
    const tag = document.createElement('div');
    tag.className = 'filter-tag';
    tag.innerHTML = `
        ${text}
        <i class="fas fa-times"></i>
    `;
    tag.onclick = onRemove;
    const container = document.getElementById('activeFilters');
    if(container) container.appendChild(tag);
}
*/
