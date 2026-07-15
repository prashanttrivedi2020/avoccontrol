

// ── Barcode scanner (html5-qrcode) ──────────────────────────────────────────
let scanner = null;
let scannerRunning = false;

async function startScan() {
 document.getElementById('scanner-box').style.display = 'block';
    setProductMode('scan');
    document.getElementById('btn-start-scan').style.display = 'none';
    document.getElementById('btn-stop-scan').style.display = '';
    document.getElementById('scan-status').textContent = TRANS.scannerStarting;

    try {
        scanner = new Html5Qrcode('scanner-box');
        await scanner.start(
            { facingMode: 'environment' },
            {
                fps: 12,
                qrbox: { width: 260, height: 120 },
                formatsToSupport: [
                    Html5QrcodeSupportedFormats.EAN_13,
                    Html5QrcodeSupportedFormats.EAN_8,
                    Html5QrcodeSupportedFormats.UPC_A,
                    Html5QrcodeSupportedFormats.UPC_E,
                    Html5QrcodeSupportedFormats.CODE_128,
                    Html5QrcodeSupportedFormats.CODE_39,
                ],
                experimentalFeatures: { useBarCodeDetectorIfSupported: true },
                rememberLastUsedCamera: false,
                showTorchButtonIfSupported: true,
            },
            (barcode) => {
                stopScan();
                document.getElementById('scan-status').textContent = TRANS.barcodeDetected + barcode;
                lookupBarcode(barcode);
            },
            () => { /* ignore frame errors */ }
        );
        scannerRunning = true;
        document.getElementById('scan-status').textContent = TRANS.holdInFrame;
        document.getElementById('scanner-hint').style.display = '';
    } catch (err) {
        console.error(err);
        document.getElementById('scan-status').textContent = '⚠️ ' + TRANS.scanError + ' (' + (err.message || err) + ')';
        document.getElementById('btn-start-scan').style.display = '';
        document.getElementById('btn-stop-scan').style.display = 'none';
    }
}

async function stopScan() {
    if (scanner && scannerRunning) {
        try { await scanner.stop(); } catch(e) {}
        try { scanner.clear(); } catch(e) {}
        scannerRunning = false;
        scanner = null;
    }
    document.getElementById('btn-start-scan').style.display = '';
    document.getElementById('btn-stop-scan').style.display = 'none';
}

// ── Barcode lookup ───────────────────────────────────────────────────────────
async function lookupBarcode(barcode) {
   document.getElementById('scanner-box').style.display = 'block';
    if (!barcode) return;
    clearProductError();
    document.getElementById('scan-status').textContent = TRANS.searching + barcode + '…';

    try {
        const res = await fetch('/api/products/barcode?barcode=' + encodeURIComponent(barcode), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (res.ok) {
            const product = await res.json();
            setConfirmedProduct(product.id, product.name, product.purchase_price, product.supplier, product.unit);
            document.getElementById('scan-status').textContent = TRANS.productFound;
            document.getElementById('scanner-box').style.display = 'none';
        } else {
            showProductError(TRANS.noProductMsg + ' <strong>' + barcode + '</strong>');
            document.getElementById('scan-status').textContent = TRANS.productNotFound;
        }
    } catch (e) {
        showProductError(TRANS.networkError);
        document.getElementById('scan-status').textContent = TRANS.networkError;
    }
}

// ── Name-based product lookup ──────────────────────────────────────────────
let productSearchDebounce = null;

function debouncedSearchProductsByName(query) {
    if (productSearchDebounce) clearTimeout(productSearchDebounce);
    productSearchDebounce = setTimeout(() => searchProductsByName(query), 250);
}

async function searchProductsByName(query) {
    const input = document.getElementById('product-name-search-input');
    const results = document.getElementById('product-name-results');
    if (!input || !results) return;

    const cleaned = (query || '').trim();
    if (cleaned.length < 3) {
        results.innerHTML = '';
        results.style.display = 'none';
        return;
    }

    results.innerHTML = '<div style="padding:10px 12px;color:var(--text-muted)">Searching…</div>';
    results.style.display = 'block';

    try {
        const res = await fetch('/api/products/search?query=' + encodeURIComponent(cleaned), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        const products = Array.isArray(data.products) ? data.products : [];

        if (!products.length) {
            results.innerHTML = '<div style="padding:10px 12px;color:var(--text-muted)">No matching products found.</div>';
            return;
        }

        const fragment = document.createDocumentFragment();
        products.forEach((product) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'product-search-result';
            button.style.cssText = 'display:block;width:100%;text-align:left;padding:10px 12px;border:0;background:#fff;cursor:pointer;';
            button.innerHTML = '<div style="font-weight:600;color:var(--text)">' + escapeHtml(product.name) + '</div>' +
                '<div style="font-size:12px;color:var(--text-muted);margin-top:2px">' + escapeHtml(product.unit || 'Stk') + (product.purchase_price ? ' · ' + escapeHtml(product.purchase_price) + ' €' : '') + '</div>';
            button.addEventListener('click', () => selectProductFromSearchResult(product));
            fragment.appendChild(button);
        });

        results.innerHTML = '';
        results.appendChild(fragment);
        results.style.display = 'block';
    } catch (e) {
        results.innerHTML = '<div style="padding:10px 12px;color:var(--text-muted)">Unable to search products right now.</div>';
    }
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function selectProductFromSearchResult(product) {
    if (!product || !product.id) return;
    setConfirmedProduct(product.id, product.name, product.purchase_price, product.supplier, product.unit);
    const input = document.getElementById('product-name-search-input');
    const results = document.getElementById('product-name-results');
    if (input) input.value = product.name;
    if (results) {
        results.innerHTML = '';
        results.style.display = 'none';
    }
}

// ── Dropdown selection ───────────────────────────────────────────────────────
function selectProductFromDropdown(sel) {
    if (!sel.value) { clearProduct(); return; }
    const opt = sel.options[sel.selectedIndex];
    setConfirmedProduct(
        sel.value,
        opt.getAttribute('data-name'),
        opt.getAttribute('data-price'),
        opt.getAttribute('data-supplier'),
        opt.getAttribute('data-unit')
    );
}

// ── Set / clear confirmed product ────────────────────────────────────────────
function setConfirmedProduct(id, name, price, supplier, unit) {
    document.getElementById('product-id-hidden').value = id;

    if (price)    document.getElementById('price-input').value    = price;
    if (supplier) document.getElementById('supplier-input').value = supplier;
    if (unit) {
        const s = document.getElementById('unit-select');
        for (let i = 0; i < s.options.length; i++) {
            if (s.options[i].value === unit) { s.selectedIndex = i; break; }
        }
    }

    document.getElementById('confirmed-name').textContent = name;
    const meta = [];
    if (price)    meta.push(TRANS.ek + ' ' + parseFloat(price).toFixed(2) + ' €');
    if (supplier) meta.push(supplier);
    if (unit)     meta.push(unit);
    document.getElementById('confirmed-meta').textContent = meta.join(' · ') || '–';
    document.getElementById('product-confirmed').classList.add('show');
    clearProductError();
}

function clearProduct() {
    document.getElementById('product-id-hidden').value = '';
    document.getElementById('product-confirmed').classList.remove('show');
    document.getElementById('price-input').value    = '';
    document.getElementById('supplier-input').value = '';
    const searchInput = document.getElementById('product-name-search-input');
    const results = document.getElementById('product-name-results');
    if (searchInput) searchInput.value = '';
    if (results) {
        results.innerHTML = '';
        results.style.display = 'none';
    }
    document.getElementById('scan-status').textContent = TRANS.cameraAccessMsg || 'Camera access will be requested when the scanner starts.';
    document.getElementById('scanner-box').style.display = 'block';
}

function showProductError(msg) {
    const el = document.getElementById('product-error');
    el.innerHTML = '❌ ' + msg;
    el.style.display = '';
}

function clearProductError() {
    document.getElementById('product-error').style.display = 'none';
}

// ── Photo mode switching ─────────────────────────────────────────────────────
function setPhotoMode(mode) {
    document.getElementById('photo-mode-camera').style.display = mode === 'camera' ? '' : 'none';
    document.getElementById('photo-mode-upload').style.display = mode === 'upload' ? '' : 'none';
    document.getElementById('photo-tab-camera').classList.toggle('active', mode === 'camera');
    document.getElementById('photo-tab-upload').classList.toggle('active', mode === 'upload');
    if (mode !== 'camera') stopPhotoCamera();
}

// ── Photo camera ─────────────────────────────────────────────────────────────
let photoStream = null;
let currentFacingMode = 'environment';

async function startPhotoCamera() {
   
    try {
        photoStream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: currentFacingMode, width: { ideal: 1920 }, height: { ideal: 1080 } },
            audio: false,
        });
        const video = document.getElementById('photo-video');
     
        video.srcObject = photoStream;
        video.setAttribute('playsinline', '');
        video.setAttribute('muted', '');
        await video.play();
        document.getElementById('camera-wrap').style.display = '';
        document.getElementById('camera-start-wrap').style.display = 'none';
        document.getElementById('photo-preview-wrap').style.display = 'none';
     
        Html5Qrcode.getCameras().then(cams => {
            document.getElementById('btn-switch-cam').style.display = cams.length > 1 ? '' : 'none';
        }).catch(() => {
            document.getElementById('btn-switch-cam').style.display = 'none';
        });
     
    } catch (err) {
        alert(TRANS.cameraAccessDenied + (err.message || err));
    }
}

async function switchCamera() {
    currentFacingMode = currentFacingMode === 'environment' ? 'user' : 'environment';
    stopPhotoCamera();
    await startPhotoCamera();
}

function stopPhotoCamera() {
    if (photoStream) {
        photoStream.getTracks().forEach(t => t.stop());
        photoStream = null;
    }
    document.getElementById('camera-wrap').style.display = 'none';
    document.getElementById('camera-start-wrap').style.display = '';
}

function capturePhoto() {
    const video  = document.getElementById('photo-video');
    const canvas = document.getElementById('photo-canvas');
    canvas.width  = video.videoWidth  || 1280;
    canvas.height = video.videoHeight || 720;
    canvas.getContext('2d').drawImage(video, 0, 0);

    const dataUrl = canvas.toDataURL('image/jpeg', 0.88);
    document.getElementById('photo-data-input').value = dataUrl;
    document.getElementById('photo-preview-img').src   = dataUrl;

    stopPhotoCamera();
    document.getElementById('photo-preview-wrap').style.display = '';
    document.getElementById('camera-start-wrap').style.display = 'none';
    document.getElementById('file-drop-text').style.display = 'none';
    document.getElementById('file-drop-icon').style.display = 'none';
}

function retakePhoto() {
    deletePhoto();
    startPhotoCamera();
}

function deletePhoto() {
    document.getElementById('photo-data-input').value = '';
    document.getElementById('photo-preview-wrap').style.display = 'none';
    document.getElementById('camera-start-wrap').style.display  = '';
}

// ── File upload drag-and-drop ─────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const nameInput = document.getElementById('product-name-search-input');
    if (nameInput) {
        nameInput.addEventListener('focus', () => {
            if (nameInput.value.trim().length >= 3) {
                debouncedSearchProductsByName(nameInput.value);
            }
        });
    }

    // const dropZone = document.getElementById('file-drop-zone');
    // if (dropZone) {
    //     dropZone.addEventListener('dragover', e => {
    //         e.preventDefault();
    //         dropZone.classList.add('dragover');
    //     });
    //     dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
    //     dropZone.addEventListener('drop', e => {
    //         e.preventDefault();
    //         dropZone.classList.remove('dragover');
    //         const file = e.dataTransfer.files[0];
    //         if (!file) return;
    //         const dt = new DataTransfer();
    //         dt.items.add(file);
    //         const input = document.getElementById('photo-file-input');
    //         input.files = dt.files;
    //         onFileChosen(input);
    //     });
    // }
});

function onFileChosen(input) {
    setPhotoMode('upload');
    const file = input.files[0];
    if (!file) return;
    const nameEl = document.getElementById('file-drop-name');
    nameEl.textContent = file.name;
    nameEl.style.display = 'none';

    document.getElementById('file-drop-text').style.display = 'none';
    document.getElementById('file-drop-icon').style.display = 'none';
    const preview = document.getElementById('file-drop-preview');
    preview.src = URL.createObjectURL(file);
}
