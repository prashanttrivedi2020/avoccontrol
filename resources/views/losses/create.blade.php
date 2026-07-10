@extends('layouts.app')

@section('title', __('Record Loss') . ' – FireKontrol 365')
@section('page-title', __('Record Loss'))

@section('topbar-actions')
    <a href="{{ route('losses.index') }}" class="btn btn-secondary">{{ __('← Back') }}</a>
@endsection

@push('styles')
    <link rel="stylesheet"
      href="{{ asset('css/losses-create.css') }}?v={{ filemtime(public_path('css/losses-create.css')) }}">
@endpush

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
        <div id="mode-scan" class="product-mode" style="display:none">
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
    cameraAccessMsg:    @json(__('Camera access will be requested when the scanner starts.')),
};


</script>

<script src="{{ asset('js/losses-create.js') }}?v={{ filemtime(public_path('js/losses-create.js')) }}"></script>
@endsection
