/**
 * Unit & Reason Management for Loss Form
 */

let allUnits = [];
let allReasons = [];

// Load units and reasons on page load
document.addEventListener('DOMContentLoaded', () => {
    loadUnits();
    loadReasons();
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
 * Load reasons from API
 */
function loadReasons() {
    fetch('/api/reasons')
        .then(res => res.json())
        .then(reasons => {
            allReasons = reasons;
            populateReasonSelect();
        })
        .catch(err => {
            console.error('Error loading reasons:', err);
        });
}

/**
 * Populate reason select dropdown
 */
function populateReasonSelect() {
    const select = document.getElementById('reason-select');
    if (!select) return;

    const currentValue = select.value;
    select.innerHTML = '';

    const defaultOption = document.createElement('option');
    defaultOption.value = '';
    defaultOption.textContent = 'Select reason…';
    select.appendChild(defaultOption);

    allReasons.forEach(reason => {
        const option = document.createElement('option');
        option.value = reason.name;
        option.textContent = reason.name;
        if (currentValue === reason.name) {
            option.selected = true;
        }
        select.appendChild(option);
    });

    select.addEventListener('change', updateReasonTableSelection);
    populateReasonTable();
}

/**
 * Populate reason table below the select.
 */
function populateReasonTable() {
    const tbody = document.getElementById('reason-table-body');
    const select = document.getElementById('reason-select');
    if (!tbody) return;

    tbody.innerHTML = '';
    if (allReasons.length === 0) {
        const row = document.createElement('tr');
        const cell = document.createElement('td');
        cell.colSpan = 1;
        cell.style.padding = '12px';
        cell.style.color = 'var(--text-muted)';
        cell.textContent = 'No reasons yet.';
        row.appendChild(cell);
        tbody.appendChild(row);
        return;
    }

    allReasons.forEach(reason => {
        const row = document.createElement('tr');
        row.style.cursor = 'pointer';
        row.style.borderTop = '1px solid var(--border)';
        row.addEventListener('click', () => {
            if (select) {
                select.value = reason.name;
                updateReasonTableSelection();
            }
        });

        const cell = document.createElement('td');
        cell.style.padding = '10px 12px';
        cell.textContent = reason.name;
        if (select && select.value === reason.name) {
            cell.style.fontWeight = '700';
            cell.style.backgroundColor = 'rgba(0, 123, 255, 0.08)';
        }
        row.appendChild(cell);
        tbody.appendChild(row);
    });
}

function updateReasonTableSelection() {
    const select = document.getElementById('reason-select');
    const rows = document.querySelectorAll('#reason-table-body tr td');
    rows.forEach(cell => {
        if (select && select.value === cell.textContent) {
            cell.style.fontWeight = '700';
            cell.style.backgroundColor = 'rgba(0, 123, 255, 0.08)';
        } else {
            cell.style.fontWeight = '400';
            cell.style.backgroundColor = 'transparent';
        }
    });
}

function showInlineReasonEditor() {
    const input = document.getElementById('inline-reason-name-input');
    if (input) {
        input.scrollIntoView({ behavior: 'smooth', block: 'center' });
        input.focus();
    }
}

function getReasonFormElements() {
    const nameInput = document.getElementById('reason-name-input') || document.getElementById('inline-reason-name-input');
    const errorEl = document.getElementById('reason-form-error') || document.getElementById('reason-inline-error');
    const successEl = document.getElementById('reason-form-success') || document.getElementById('reason-inline-success');
    const submitBtn = document.getElementById('reason-submit-btn') || document.getElementById('reason-inline-submit-btn');
    return { nameInput, errorEl, successEl, submitBtn };
}

function clearReasonForm() {
    const nameInput = document.getElementById('reason-name-input') || document.getElementById('inline-reason-name-input');
    if (nameInput) nameInput.value = '';
    const errorEl = document.getElementById('reason-form-error') || document.getElementById('reason-inline-error');
    if (errorEl) errorEl.textContent = '';
    const successEl = document.getElementById('reason-form-success') || document.getElementById('reason-inline-success');
    if (successEl) successEl.textContent = '';
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
 * Open reason modal
 */
function openReasonModal() {
    const modal = document.getElementById('reason-modal');
    if (modal) {
        modal.classList.add('show');
        document.getElementById('reason-name-input').focus();
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
 * Close reason modal
 */
function closeReasonModal() {
    const modal = document.getElementById('reason-modal');
    if (modal) {
        modal.classList.remove('show');
        clearReasonForm();
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
 * Open product modal
 */
function openProductModal() {
    const modal = document.getElementById('product-modal');
    if (modal) {
        modal.classList.add('show');
        document.getElementById('product-name-input').focus();
    }
}

/**
 * Close product modal
 */
function closeProductModal() {
    const modal = document.getElementById('product-modal');
    if (modal) {
        modal.classList.remove('show');
        clearProductForm();
    }
}

/**
 * Clear product form
 */
function clearProductForm() {
    const nameInput = document.getElementById('product-name-input');
    if (nameInput) nameInput.value = '';
    const errorEl = document.getElementById('product-form-error');
    if (errorEl) errorEl.textContent = '';
    const successEl = document.getElementById('product-form-success');
    if (successEl) successEl.textContent = '';
}

/**
 * Submit new product
 */
async function submitNewProduct() {
    const nameInput = document.getElementById('product-name-input');
    const errorEl = document.getElementById('product-form-error');
    const successEl = document.getElementById('product-form-success');
    const submitBtn = document.getElementById('product-submit-btn');

    if (!nameInput || !errorEl || !successEl || !submitBtn) return;

    const name = nameInput.value.trim();
    errorEl.textContent = '';
    successEl.textContent = '';

    if (!name) {
        errorEl.textContent = 'Product name is required';
        return;
    }

    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Saving...';
    submitBtn.classList.add('unit-loading');

    try {
        const response = await fetch('/api/products', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                name,
                unit: document.getElementById('unit-select')?.value || 'Stk',
            }),
        });

        const data = await response.json();

        if (!response.ok) {
            errorEl.textContent = data.message || 'Error creating product';
            submitBtn.textContent = originalText;
            submitBtn.classList.remove('unit-loading');
            return;
        }

        if (typeof setConfirmedProduct === 'function') {
            setConfirmedProduct(data.product.id, data.product.name, data.product.purchase_price, data.product.supplier, data.product.unit);
        }

        successEl.textContent = `✓ ${data.product.name} added successfully!`;
        submitBtn.textContent = originalText;
        submitBtn.classList.remove('unit-loading');

        setTimeout(() => {
            closeProductModal();
        }, 1000);
    } catch (err) {
        console.error('Error:', err);
        errorEl.textContent = 'Network error. Please try again.';
        submitBtn.textContent = originalText;
        submitBtn.classList.remove('unit-loading');
    }
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
 * Submit new reason
 */
async function submitNewReason() {
    const { nameInput, errorEl, successEl, submitBtn } = getReasonFormElements();

    if (!nameInput || !errorEl || !successEl || !submitBtn) return;

    const name = nameInput.value.trim();
    errorEl.textContent = '';
    successEl.textContent = '';

    if (!name) {
        errorEl.textContent = 'Reason name is required';
        return;
    }

    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Saving...';
    submitBtn.classList.add('unit-loading');

    try {
        const response = await fetch('/api/reasons', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ name }),
        });

        const data = await response.json();

        if (!response.ok) {
            errorEl.textContent = data.message || 'Error creating reason';
            submitBtn.textContent = originalText;
            submitBtn.classList.remove('unit-loading');
            return;
        }

        allReasons.push(data.reason);
        populateReasonSelect();

        const select = document.getElementById('reason-select');
        if (select) {
            select.value = data.reason.name;
        }

        successEl.textContent = `✓ ${data.reason.name} added successfully!`;
        submitBtn.textContent = originalText;
        submitBtn.classList.remove('unit-loading');

        setTimeout(() => {
            closeReasonModal();
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
    const productInput = document.getElementById('product-name-input');
    if (productInput) {
        productInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                submitNewProduct();
            }
        });
    }

    const reasonInput = document.getElementById('reason-name-input');
    if (reasonInput) {
        reasonInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                submitNewReason();
            }
        });
    }

    // Close modal on backdrop click
    const unitModal = document.getElementById('unit-modal');
    if (unitModal) {
        unitModal.addEventListener('click', (e) => {
            if (e.target === unitModal) {
                closeUnitModal();
            }
        });
    }

    const productModal = document.getElementById('product-modal');
    if (productModal) {
        productModal.addEventListener('click', (e) => {
            if (e.target === productModal) {
                closeProductModal();
            }
        });
    }

    const reasonModal = document.getElementById('reason-modal');
    if (reasonModal) {
        reasonModal.addEventListener('click', (e) => {
            if (e.target === reasonModal) {
                closeReasonModal();
            }
        });
    }
});
