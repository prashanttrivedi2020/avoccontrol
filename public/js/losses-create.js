

// ── Barcode scanner (html5-qrcode) ──────────────────────────────────────────
let scanner = null;
let scannerRunning = false;

async function startScan() {
 
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
        } else {
            showProductError(TRANS.noProductMsg + ' <strong>' + barcode + '</strong>');
            document.getElementById('scan-status').textContent = TRANS.productNotFound;
        }
    } catch (e) {
        showProductError(TRANS.networkError);
        document.getElementById('scan-status').textContent = TRANS.networkError;
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
    document.getElementById('scan-status').textContent = TRANS.cameraAccessMsg || 'Camera access will be requested when the scanner starts.';
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
    document.getElementById('camera-start-wrap').style.display  = 'none';
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
