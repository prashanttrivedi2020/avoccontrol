/**
 * Unit Management for Loss Form
 */

let allUnits = [];

// Load units on page load
document.addEventListener('DOMContentLoaded', () => {
    loadUnits();
});

/**
 * Load units from API
 */
function loadUnits() {
    fetch('/api/units')
        .then(res => res.json())
        .then(units => {
            allUnits = units;
            populateUnitSelect();
        })
        .catch(err => {
            console.error('Error loading units:', err);
        });
}

/**
 * Populate unit select dropdown
 */
function populateUnitSelect() {
    const select = document.getElementById('unit-select');
    if (!select) return;

    const currentValue = select.value;
    select.innerHTML = '';

    allUnits.forEach(unit => {
        const option = document.createElement('option');
        option.value = unit.name;
        option.textContent = unit.name;
        if (currentValue === unit.name) {
            option.selected = true;
        }
        select.appendChild(option);
    });
}

/**
 * Open unit modal
 */
function openUnitModal() {
    const modal = document.getElementById('unit-modal');
    if (modal) {
        modal.classList.add('show');
        document.getElementById('unit-name-input').focus();
    }
}

/**
 * Close unit modal
 */
function closeUnitModal() {
    const modal = document.getElementById('unit-modal');
    if (modal) {
        modal.classList.remove('show');
        clearUnitForm();
    }
}

/**
 * Clear unit form
 */
function clearUnitForm() {
    document.getElementById('unit-name-input').value = '';
    document.getElementById('unit-form-error').textContent = '';
    document.getElementById('unit-form-success').textContent = '';
}

/**
 * Submit new unit
 */
async function submitNewUnit() {
    const name = document.getElementById('unit-name-input').value.trim();
    const errorEl = document.getElementById('unit-form-error');
    const successEl = document.getElementById('unit-form-success');

    // Validation
    errorEl.textContent = '';
    successEl.textContent = '';

    if (!name) {
        errorEl.textContent = 'Unit name is required';
        return;
    }

    // Show loading state
    const submitBtn = document.getElementById('unit-submit-btn');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Saving...';
    submitBtn.classList.add('unit-loading');

    try {
        const response = await fetch('/api/units', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ name }),
        });

        const data = await response.json();

        if (!response.ok) {
            errorEl.textContent = data.message || 'Error creating unit';
            submitBtn.textContent = originalText;
            submitBtn.classList.remove('unit-loading');
            return;
        }

        // Success
        allUnits.push(data.unit);
        populateUnitSelect();

        // Set the new unit as selected
        const select = document.getElementById('unit-select');
        select.value = data.unit.name;

        successEl.textContent = `✓ ${data.unit.name} added successfully!`;
        submitBtn.textContent = originalText;
        submitBtn.classList.remove('unit-loading');

        // Close modal after a short delay
        setTimeout(() => {
            closeUnitModal();
        }, 1000);
    } catch (err) {
        console.error('Error:', err);
        errorEl.textContent = 'Network error. Please try again.';
        submitBtn.textContent = originalText;
        submitBtn.classList.remove('unit-loading');
    }
}

/**
 * Handle Enter key in unit form
 */
document.addEventListener('DOMContentLoaded', () => {
    const descInput = document.getElementById('unit-description-input');
    if (descInput) {
        descInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                submitNewUnit();
            }
        });
    }

    // Close modal on backdrop click
    const modal = document.getElementById('unit-modal');
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeUnitModal();
            }
        });
    }
});
