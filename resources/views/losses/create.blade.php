@extends('layouts.app')

@section('title', __('Record Loss') . ' – FireKontrol 365')
@section('page-title', __('Record Loss'))

@section('topbar-actions')
    <a href="{{ route('losses.index') }}" class="btn btn-secondary">{{ __('← Back') }}</a>
@endsection

@section('content')

<div class="card">

@if($products->isEmpty())
    <div class="alert alert-info">
        ℹ️ {{ __('Please create products first') }}
        <a href="{{ route('products.create') }}" style="color:inherit;font-weight:700">{{ __('Create products') }}</a>
        {{ __('before recording losses.') }}
    </div>
@else
<form method="POST" action="{{ route('losses.store') }}" enctype="multipart/form-data" id="loss-form">
    @csrf
    {{-- ════════════════════════════════════════════════════════
         SECTION 4: PHOTO
         ════════════════════════════════════════════════════════ --}}
    <div class="form-group">
        <label class="form-label">{{ __('Photo') }}<span class="form-hint"> {{ __('Optional: photo as evidence (max. 10 MB)') }}</span></label> 
         {{-- Camera capture --}}
        <div id="photo-mode-camera">
            <div id="camera-wrap" style="display:none;position:relative;border-radius:12px;overflow:hidden;background:#000;margin-bottom:10px">
                <video id="photo-video" autoplay playsinline muted
                       style="width:100%;max-height:320px;object-fit:cover;display:block"></video>
                <div style="position:absolute;bottom:0;left:0;right:0;padding:12px;
                            background:linear-gradient(transparent,rgba(0,0,0,0.7));
                            display:flex;justify-content:center;gap:10px">
                    <button type="button" class="btn btn-primary" onclick="capturePhoto()" style="font-size:15px">
                        📸 {{ __('Take photo') }}
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="stopPhotoCamera()">
                        ⏹ {{ __('Stop') }}
                    </button>
                </div>
                <button type="button" onclick="switchCamera()" id="btn-switch-cam"
                        style="position:absolute;top:10px;right:10px;
                               background:rgba(0,0,0,0.55);border:none;border-radius:8px;
                               color:#fff;padding:6px 10px;cursor:pointer;font-size:18px"
                        title="{{ __('Switch camera') }}">🔄</button>
            </div>
            <canvas id="photo-canvas" style="display:none"></canvas>

            <div id="camera-start-wrap">
                <div style="display: none;">
                    <button type="button" class="btn btn-primary" onclick="startPhotoCamera()">
                        📷 {{ __('Open camera') }}
                    </button>
                    <div class="form-hint" style="margin-top:6px">
                        {{ __('Opens the back camera to photograph the damage') }}
                    </div>
                </div>
            </div>

            <div id="photo-preview-wrap" style="display:none;margin-top:10px">
                <div style="display:flex;align-items:flex-start;gap:12px">
                    <img id="photo-preview-img"
                         style="width:100px;height:100px;object-fit:cover;border-radius:8px;border:2px solid var(--border)">
                    <div>
                        <div style="font-size:13px;font-weight:600;color:var(--text);margin-bottom:6px">
                            ✅ {{ __('Photo taken') }}
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="retakePhoto()">
                            🔄 {{ __('Retake') }}
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="deletePhoto()" style="margin-left:6px">
                            🗑 {{ __('Delete') }}
                        </button>
                    </div>
                </div>
            </div>

            <input type="hidden" name="photo_data" id="photo-data-input">
        </div>

        {{-- File upload --}}
        <div id="file-drop-icon" style="font-size:36px;margin-bottom:8px;display:none;">🖼️{{ __('no_photo_yet') }}</div>
        <div id="photo-mode-upload" style="display:block;width:49%;height:auto;margin-left: 25%;">
            <div id="file-drop-zone" style="
                border:2px dashed var(--border);border-radius:12px;
                text-align:center;cursor:pointer;
                transition:border-color .2s,background .2s;
            " onclick="document.getElementById('photo-file-input').click()">
               
                <div id="file-drop-text" style="color:var(--text-muted);font-size:13px">
                    🖼️
                  {{ __('no_photo_yet') }}
                </div>
                <div id="file-drop-name" style="margin-top:8px;font-weight:600;display:block;">
                   

                </div>
                 <img  id="file-drop-preview" style="width:100%;height:auto;object-fit:cover;border-radius:8px;margin-bottom:6px">
                <input type="file" id="photo-file-input" name="photo" accept="image/*"
                       style="position:absolute;opacity:0;width:0;height:0"
                       onchange="onFileChosen(this)">
            </div>
           
        </div>

        <div class="mode-tabs" id="photo-mode-tabs" style="margin-bottom:12px">
            <!-- <button type="button" class="mode-tab active" onclick="setPhotoMode('camera')" id="photo-tab-camera">
                <span>📷</span> {{ __('Capture with camera') }}
            </button> -->

             <button type="button" class="mode-tab active" onclick="startPhotoCamera()" id="photo-tab-camera">
                <span>📷</span> {{ __('Capture with camera') }}
            </button>

            <!-- <button type="button" class="mode-tab" onclick="setPhotoMode('upload')" id="photo-tab-upload">
                <span>📁</span> {{ __('Upload file') }}
            </button> -->
            <button type="button" class="mode-tab" onclick="document.getElementById('photo-file-input').click()" id="photo-tab-upload">
                <span>📁</span> {{ __('Upload file') }}
            </button>
           
        </div>

       

        @error('photo') <div class="form-error">{{ $message }}</div> @enderror
    </div>


    {{-- ════════════════════════════════════════════════════════
         SECTION 1: PRODUCT SELECTION
         ════════════════════════════════════════════════════════ --}}
    <div class="form-group">
        <label class="form-label">{{ __('Product') }} *</label>

        {{-- Mode tabs --}}
        <div class="mode-tabs" id="product-mode-tabs">
            <!-- <button type="button" class="mode-tab" onclick="setProductMode('scan')" id="tab-scan">
                <span>📷</span> {{ __('Scan barcode') }}
            </button> -->

            <button type="button" class="mode-tab" onclick="startScan()" id="tab-scan">
                <span>📷</span> {{ __('Scan barcode') }}
            </button>

            
            <button type="button" class="mode-tab active" onclick="setProductMode('manual-barcode')" id="tab-manual-barcode">
                <span>🔢</span> {{ __('Enter barcode') }}
            </button>
            <!-- <button type="button" class="mode-tab" onclick="setProductMode('dropdown')" id="tab-dropdown">
                <span>☰</span> {{ __('Select manually') }}
            </button> -->
        </div>

        {{-- Mode: Camera barcode scanner --}}
        <div id="mode-scan" class="product-mode">
            <div id="scanner-wrap" style="position:relative;border-radius:12px;overflow:hidden;background:#000;margin-bottom:10px">
                <div id="scanner-box"></div>
                <div id="scanner-overlay" style="
                    position:absolute;inset:0;display:flex;align-items:center;justify-content:center;
                    pointer-events:none;
                ">
                    <div style="
                        width:260px;height:120px;border:2px solid var(--accent);border-radius:8px;
                        box-shadow:0 0 0 9999px rgba(0,0,0,0.5);
                    ">
                        <div class="scan-line"></div>
                    </div>
                </div>
                <div id="scanner-hint" style="
                    position:absolute;bottom:12px;left:0;right:0;text-align:center;
                    color:#fff;font-size:13px;text-shadow:0 1px 4px #000;
                ">{{ __('Hold barcode in frame') }}</div>
            </div>
            <div style="display:flex;gap:8px">
                <button type="button" class="btn btn-primary" id="btn-start-scan" onclick="startScan()">
                    📷 {{ __('Start scanner') }}
                </button>
                <button type="button" class="btn btn-secondary" id="btn-stop-scan" onclick="stopScan()" style="display:none">
                    ⏹ {{ __('Stop') }}
                </button>
            </div>
            <div id="scan-status" style="margin-top:10px;font-size:13px;color:var(--text-muted)">
                {{ __('Camera access will be requested when the scanner starts.') }}
            </div>
        </div>

        {{-- Mode: Manual barcode text input --}}
        <div id="mode-manual-barcode" class="product-mode" style="display:none">
            <div style="display:flex;gap:8px">
                <input type="text" id="barcode-text-input" class="form-control"
                       placeholder="{{ __('Enter EAN / UPC / barcode…') }}"
                       style="font-family:monospace;letter-spacing:1px"
                       onkeydown="if(event.key==='Enter'){event.preventDefault();lookupBarcode(this.value.trim());}">
                <button type="button" class="btn btn-primary"
                        onclick="lookupBarcode(document.getElementById('barcode-text-input').value.trim())"
                        style="white-space:nowrap">
                    🔍 {{ __('Search') }}
                </button>
            </div>
            <div class="form-hint">{{ __('Type barcode manually or scan with a USB scanner') }}</div>
        </div>

        {{-- Mode: Dropdown --}}
        <div id="mode-dropdown" class="product-mode" style="display:none">
            <select id="product-dropdown" class="form-control" onchange="selectProductFromDropdown(this)">
                <option value="">{{ __('Select product…') }}</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}"
                            data-name="{{ $p->name }}"
                            data-price="{{ $p->purchase_price }}"
                            data-supplier="{{ $p->supplier ?? '' }}"
                            data-unit="{{ $p->unit }}"
                            {{ old('product_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->name }}{{ $p->category ? ' ('.$p->category.')' : '' }}
                        {{ $p->barcode ? ' · '.$p->barcode : '' }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Hidden real product_id that gets submitted --}}
        <input type="hidden" name="product_id" id="product-id-hidden" value="{{ old('product_id') }}">

        {{-- Product confirmation card --}}
        <div id="product-confirmed" style="display:none;margin-top:12px;
            background:rgba(45,122,45,0.18);border:1px solid #2d7a2d;
            border-radius:10px;padding:12px 16px;display:flex;align-items:center;gap:12px">
            <span style="font-size:24px">✅</span>
            <div style="flex:1;min-width:0">
                <div style="font-weight:700;font-size:15px;color:var(--text)" id="confirmed-name">–</div>
                <div style="font-size:12px;color:var(--text-muted);margin-top:2px" id="confirmed-meta">–</div>
            </div>
            <button type="button" onclick="clearProduct()"
                    style="background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:20px;padding:0 4px"
                    title="{{ __('Remove product') }}">×</button>
        </div>
        <div id="product-error" style="display:none;margin-top:10px" class="alert alert-error"></div>

        @error('product_id') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    {{-- ════════════════════════════════════════════════════════
         SECTION 2: DATE + REASON
         ════════════════════════════════════════════════════════ --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
        <div class="form-group">
            <label class="form-label">{{ __('Date') }} *</label>
            <input type="date" name="loss_date" class="form-control"
                   value="{{ old('loss_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}">
            @error('loss_date') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label class="form-label">{{ __('Reason') }} *</label>
            <select name="reason" class="form-control">
                <option value="">{{ __('Select reason…') }}</option>
                @foreach([
                    'verderb'       => '🍃 Spoilage',
                    'ablauf'        => '📅 Expiry / BBD',
                    'diebstahl'     => '🚨 Theft',
                    'beschaedigung' => '💥 Damage',
                    'tathergang'    => '📋 Incident report',
                    'sonstiges'     => '📎 Other',
                ] as $val => $key)
                    <option value="{{ $val }}" {{ old('reason') === $val ? 'selected' : '' }}>
                        {{ __($key) }}
                    </option>
                @endforeach
            </select>
            @error('reason') <div class="form-error">{{ $message }}</div> @enderror
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════
         SECTION 3: QUANTITY + UNIT + PRICE + SUPPLIER
         ════════════════════════════════════════════════════════ --}}
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px">
        <div class="form-group">
            <label class="form-label">{{ __('Quantity') }} *</label>
            <input type="number" name="quantity" class="form-control"
                   value="{{ old('quantity', 1) }}" step="0.001" min="0.001" placeholder="1">
            @error('quantity') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label class="form-label">{{ __('Unit') }} *</label>
            <select name="unit" id="unit-select" class="form-control">
                @foreach(['Stk', 'kg', 'g', 'L', 'ml', 'Pkg', 'Bund', 'Kiste'] as $unit)
                    <option value="{{ $unit }}" {{ old('unit', 'Stk') === $unit ? 'selected' : '' }}>{{ $unit }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
        <div class="form-group">
            <label class="form-label">{{ __('Purchase price (€)') }}</label>
            <input type="number" name="purchase_price" id="price-input" class="form-control"
                   value="{{ old('purchase_price') }}" step="0.01" min="0" placeholder="0.00">
            <div class="form-hint">{{ __('Pre-filled from product') }}</div>
        </div>
        <div class="form-group">
            <label class="form-label">{{ __('Supplier') }}</label>
            <input type="text" name="supplier" id="supplier-input" class="form-control"
                   value="{{ old('supplier') }}" placeholder="{{ __('e.g. Northern Dairy') }}">
        </div>
    </div>

    
    {{-- ════════════════════════════════════════════════════════
         SECTION 5: NOTES + SUBMIT
         ════════════════════════════════════════════════════════ --}}
    <div class="form-group">
        <label class="form-label">{{ __('Notes') }}</label>
        <textarea name="notes" class="form-control" rows="3"
                  placeholder="{{ __('e.g. incident number, batch number, etc.') }}">{{ old('notes') }}</textarea>
    </div>

    <div style="display:flex;gap:10px;margin-top:8px">
        <button type="submit" class="btn btn-primary">💾 {{ __('Save loss entry') }}</button>
        <a href="{{ route('losses.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
    </div>
</form>
@endif

</div><!-- /.card -->


<style>
.mode-tabs {
    display: flex;
    gap: 4px;
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 4px;
    margin-bottom: 12px;
}
.mode-tab {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 10px;
    border: none;
    border-radius: 7px;
    background: transparent;
    color: var(--text-muted);
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: background .15s, color .15s;
    white-space: nowrap;
}
.mode-tab.active {
    background: var(--card2);
    color: var(--text);
    box-shadow: 0 1px 3px rgba(0,0,0,0.3);
}
.mode-tab:hover:not(.active) { color: var(--text); background: rgba(255,255,255,.05); }
.product-mode { animation: fadeIn .2s ease; }
@keyframes fadeIn { from { opacity:0; transform:translateY(4px); } to { opacity:1; transform:none; } }
.scan-line {
    position: absolute;
    left: 0; right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, var(--accent), transparent);
    animation: scanMove 1.8s ease-in-out infinite;
    top: 0;
}
@keyframes scanMove {
    0%   { top: 10%;  opacity: 1; }
    50%  { top: 85%;  opacity: 1; }
    100% { top: 10%;  opacity: 1; }
}
#scanner-box video { border-radius: 0 !important; }
#scanner-box { min-height: 240px; }
#file-drop-zone.dragover { border-color: var(--accent); background: rgba(232,34,58,.06); }
#product-confirmed { display: none; }
#product-confirmed.show { display: flex !important; }
</style>

<script src="/js/html5-qrcode.min.js"></script>
<script>
// ── Translated strings passed from PHP ──────────────────────────────────────
const TRANS = {
    scannerStarting:    @json(__('Camera is starting…')),
    holdInFrame:        @json(__('Hold barcode in frame…')),
    barcodeDetected:    @json(__('✅ Barcode detected: ')),
    productNotFound:    @json(__('No product found')),
    productFound:       @json(__('✅ Product found!')),
    networkError:       @json(__('Network error during barcode search.')),
    scanError:          @json(__('Camera error. Please use "Enter barcode".')),
    cameraAccessDenied: @json(__('Camera access denied: ')),
    searching:          @json(__('🔍 Searching for barcode ')),
    noProductMsg:       @json(__('No product with barcode found. Please select manually.')),
    ek:                 @json(__('PP:')),
};

// ── Product data ─────────────────────────────────────────────────────────────
const PRODUCTS = {
    @foreach($products as $p)
    {{ $p->id }}: {
        name: @json($p->name),
        price: {{ $p->purchase_price ?? 'null' }},
        supplier: @json($p->supplier ?? ''),
        unit: @json($p->unit),
        category: @json($p->category ?? ''),
    },
    @endforeach
};

// ── Product mode switching ───────────────────────────────────────────────────
function setProductMode(mode) {
    // ['scan', 'manual-barcode', 'dropdown'].forEach(m => {
    //     document.getElementById('mode-' + m).style.display = m === mode ? '' : 'none';
    //     document.getElementById('tab-' + m).classList.toggle('active', m === mode);
    // });
    // if (mode !== 'scan') stopScan();

    ['scan', 'manual-barcode'].forEach(m => {
        document.getElementById('mode-' + m).style.display = m === mode ? '' : 'none';
        document.getElementById('tab-' + m).classList.toggle('active', m === mode);
    });
    if (mode !== 'scan') stopScan();
}

// ── Barcode scanner (html5-qrcode) ──────────────────────────────────────────
let scanner = null;
let scannerRunning = false;
setProductMode('manual-barcode');
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
    document.getElementById('scan-status').textContent = @json(__('Camera access will be requested when the scanner starts.'));
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
const dropZone = document.getElementById('file-drop-zone');
if (dropZone) {
  
    dropZone.addEventListener('dragover', e => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        const file = e.dataTransfer.files[0];
        if (!file) return;
        const dt = new DataTransfer();
        dt.items.add(file);
        const input = document.getElementById('photo-file-input');
        input.files = dt.files;
        onFileChosen(input);
    });
}

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

// ── Pre-select product if old('product_id') is set ───────────────────────────
@if(old('product_id'))
(function() {
    const id = {{ (int) old('product_id') }};
    const p  = PRODUCTS[id];
    if (p) setConfirmedProduct(id, p.name, p.price, p.supplier, p.unit);
    //setProductMode('dropdown');
     setProductMode('manual-barcode');
    
    const dd = document.getElementById('product-dropdown');
    if (dd) dd.value = id;
})();
@endif
</script>
@endsection
