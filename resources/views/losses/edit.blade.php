@extends('layouts.app')

@section('title', __('Edit Loss Entry') . ' – FireKontrol 365')
@section('page-title', __('Edit Loss Entry'))

@section('topbar-actions')
    <a href="{{ route('losses.index') }}" class="btn btn-secondary">{{ __('← Back') }}</a>
@endsection

@push('styles')
    <link rel="stylesheet"
      href="{{ asset('css/losses-create.css') }}?v={{ filemtime(public_path('css/losses-create.css')) }}">
@endpush

@section('content')
<div class="card" style="max-width:900px">
    <form method="POST" action="{{ route('losses.update', $loss) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">{{ __('Product') }} *</label>
            {{$loss->product->name}}
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
            <div class="form-group">
                <label class="form-label">{{ __('Date') }} *</label>
                <input type="date" name="loss_date" class="form-control" value="{{ old('loss_date', $loss->loss_date->format('Y-m-d')) }}" max="{{ date('Y-m-d') }}">
                @error('loss_date') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">{{ __('Reason') }} *</label>
                <div class="unit-select-wrapper">
                    <select name="reason" id="reason-select" class="form-control">
                        <option value="">{{ __('Select reason…') }}</option>
                        @foreach($reasons as $reasonName)
                            <option value="{{ $reasonName }}" {{ old('reason', $loss->reason) === $reasonName ? 'selected' : '' }}>
                                {{ __($reasonName) }}
                            </option>
                        @endforeach
                    </select>
                    <button type="button" class="unit-add-icon" onclick="openReasonModal()" title="{{ __('Add new reason') }}">
                        ➕
                    </button>
                </div>
                @error('reason') <div class="form-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
            <div class="form-group">
                <label class="form-label">{{ __('Quantity') }} *</label>
                <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $loss->quantity) }}" step="0.001" min="0.001">
                @error('quantity') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">{{ __('Unit') }} *</label>
                <select name="unit" class="form-control">
                    @foreach($units as $unit)
                        <option value="{{ $unit->name }}" {{ old('unit', $loss->unit) === $unit->name ? 'selected' : '' }}>
                            {{ $unit->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
            <div class="form-group">
                <label class="form-label">{{ __('Purchase price (€)') }}</label>
                <input type="number" name="purchase_price" class="form-control" value="{{ old('purchase_price', $loss->purchase_price) }}" step="0.01" min="0">
            </div>
            <div class="form-group">
                <label class="form-label">{{ __('Supplier') }}</label>
                <input type="text" name="supplier" class="form-control" value="{{ old('supplier', $loss->supplier) }}">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">{{ __('Photo') }}<span class="form-hint"> {{ __('Optional: photo as evidence (max. 10 MB)') }}</span></label>

            @if($loss->photo_path)
                <div style="margin-bottom:10px">
                    <img src="{{ asset('storage/' . $loss->photo_path) }}" alt="{{ __('Current photo') }}" style="max-width:220px;max-height:180px;object-fit:cover;border-radius:10px;border:1px solid var(--border)">
                </div>
            @endif

            <div id="photo-mode-camera">
                <div id="camera-wrap" style="display:none;position:relative;border-radius:12px;overflow:hidden;background:#000;margin-bottom:10px">
                    <video id="photo-video" autoplay playsinline muted
                           style="width:100%;max-height:320px;object-fit:cover;display:block"></video>
                    <div style="position:absolute;bottom:0;left:0;right:0;padding:12px;background:linear-gradient(transparent,rgba(0,0,0,0.7));display:flex;justify-content:center;gap:10px">
                        <button type="button" class="btn btn-primary" onclick="capturePhoto()" style="font-size:15px">
                            📸 {{ __('Take photo') }}
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="stopPhotoCamera()">
                            ⏹ {{ __('Stop') }}
                        </button>
                    </div>
                    <button type="button" onclick="switchCamera()" id="btn-switch-cam"
                            style="position:absolute;top:10px;right:10px;background:rgba(0,0,0,0.55);border:none;border-radius:8px;color:#fff;padding:6px 10px;cursor:pointer;font-size:18px"
                            title="{{ __('Switch camera') }}">🔄</button>
                </div>
                <canvas id="photo-canvas" style="display:none"></canvas>

                <div id="camera-start-wrap">
                    <div style="display:none">
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

            <div id="file-drop-icon" style="font-size:36px;margin-bottom:8px;display:none;">🖼️{{ __('no_photo_yet') }}</div>
            <div id="photo-mode-upload" style="display:block;width:49%;height:auto;margin-left:25%;">
                <div id="file-drop-zone" style="border:2px dashed var(--border);border-radius:12px;text-align:center;cursor:pointer;transition:border-color .2s,background .2s;"
                     onclick="document.getElementById('photo-file-input').click()">
                    <div id="file-drop-text" style="color:var(--text-muted);font-size:13px">
                        🖼️ {{ __('no_photo_yet') }}
                    </div>
                    <div id="file-drop-name" style="margin-top:8px;font-weight:600;display:block;"></div>
                    <img id="file-drop-preview" style="width:100%;height:auto;object-fit:cover;border-radius:8px;margin-bottom:6px">
                    <input type="file" id="photo-file-input" name="photo" accept="image/*"
                           style="position:absolute;opacity:0;width:0;height:0"
                           onchange="onFileChosen(this)">
                </div>
            </div>

            <div class="mode-tabs" id="photo-mode-tabs" style="margin-bottom:12px">
                <button type="button" class="mode-tab active" onclick="startPhotoCamera()" id="photo-tab-camera">
                    <span>📷</span> {{ __('Capture with camera') }}
                </button>
                <button type="button" class="mode-tab" onclick="document.getElementById('photo-file-input').click()" id="photo-tab-upload">
                    <span>📁</span> {{ __('Upload file') }}
                </button>
            </div>

            <label style="display:flex;align-items:center;gap:8px;margin-top:8px;font-size:13px;color:var(--text-muted)">
                <input type="checkbox" name="remove_photo" value="1">
                {{ __('Remove current photo') }}
            </label>

            @error('photo') <div class="form-error">{{ $message }}</div> @enderror
            @error('photo_data') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label class="form-label">{{ __('Notes') }}</label>
            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $loss->notes) }}</textarea>
        </div>

        <div style="display:flex;gap:10px;margin-top:8px">
            <button type="submit" class="btn btn-primary">💾 {{ __('Save changes') }}</button>
            <a href="{{ route('losses.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
        </div>
    </form>
</div>

<div id="reason-modal">
    <div id="reason-modal-content">
        <h3>{{ __('Add New Reason') }}</h3>
        <div class="unit-form-group">
            <label>{{ __('Reason Name') }} *</label>
            <input type="text" id="reason-name-input" placeholder="{{ __('e.g. Quality issue') }}"
                   onkeydown="if(event.key==='Enter'){event.preventDefault();submitNewReason();}">
        </div>
        <div id="reason-form-error" class="unit-error"></div>
        <div id="reason-form-success" class="unit-success"></div>
        <div class="unit-modal-actions">
            <button type="button" class="unit-modal-btn unit-modal-btn-secondary" onclick="closeReasonModal()">
                {{ __('Cancel') }}
            </button>
            <button type="button" class="unit-modal-btn unit-modal-btn-primary" id="reason-submit-btn" onclick="submitNewReason()">
                {{ __('Add Reason') }}
            </button>
        </div>
    </div>
</div>

<script>
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
<script src="{{ asset('js/html5-qrcode.min.js') }}?v={{ filemtime(public_path('js/html5-qrcode.min.js')) }}"></script>
<script src="{{ asset('js/losses-create.js') }}?v={{ filemtime(public_path('js/losses-create.js')) }}"></script>
@endsection
