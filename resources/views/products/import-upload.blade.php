@extends('layouts.app')

@section('title', __('Import CSV') . ' – FireKontrol 365')
@section('page-title', __('Import CSV'))

@section('topbar-actions')
    <a href="{{ route('products.index') }}" class="btn btn-secondary">{{ __('← Back') }}</a>
@endsection

@section('content')
<div style="max-width:640px">
    <div class="alert alert-info">
        ℹ️ {{ __('Upload a CSV file with your product data. You can map the columns in the next step.') }}
    </div>

    <div class="card" style="margin-bottom:20px">
        <h3 style="font-size:14px;font-weight:700;margin-bottom:12px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px">
            {{ __('Expected columns (example)') }}
        </h3>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px">
            @foreach(['name', 'barcode', 'category', 'supplier', 'purchase_price', 'unit'] as $col)
            <div style="background:var(--bg2);border:1px solid var(--border);border-radius:6px;padding:8px 12px;font-size:12px;font-family:monospace;color:var(--text-muted)">
                {{ $col }}
            </div>
            @endforeach
        </div>
        <p style="font-size:12px;color:var(--text-muted);margin-top:10px">
            {{ __('Column headers must be in the first row. Separator: comma or semicolon.') }}
        </p>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('products.import.upload.post') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label">{{ __('CSV file *') }}</label>
                <div style="border:2px dashed var(--border);border-radius:10px;padding:40px;text-align:center;transition:border-color .2s"
                     id="dropzone" ondragover="this.style.borderColor='var(--green-l)'"
                     ondragleave="this.style.borderColor='var(--border)'"
                     ondrop="handleDrop(event)">
                    <div style="font-size:32px;margin-bottom:10px">📂</div>
                    <p style="font-size:14px;font-weight:600;margin-bottom:6px">{{ __('Drag & drop CSV file here') }}</p>
                    <p style="font-size:13px;color:var(--text-muted);margin-bottom:16px">{{ __('or') }}</p>
                    <label style="cursor:pointer">
                        <span class="btn btn-secondary">{{ __('Select file') }}</span>
                        <input type="file" name="csv_file" id="csv-file" accept=".csv,.txt" style="display:none"
                               onchange="fileSelected(this)">
                    </label>
                    <p id="file-name" style="margin-top:12px;font-size:13px;color:var(--text-muted)">{{ __('No file selected') }}</p>
                </div>
                @error('csv_file') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div style="display:flex;gap:10px;margin-top:8px">
                <button type="submit" class="btn btn-primary">{{ __('Continue to mapping →') }}</button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>

<script>
function fileSelected(input) {
    const name = input.files[0]?.name ?? '';
    document.getElementById('file-name').textContent = name || @json(__('No file selected'));
}
function handleDrop(e) {
    e.preventDefault();
    document.getElementById('dropzone').style.borderColor = 'var(--border)';
    const file = e.dataTransfer.files[0];
    if (!file) return;
    const dt = new DataTransfer();
    dt.items.add(file);
    const input = document.getElementById('csv-file');
    input.files = dt.files;
    fileSelected(input);
}
</script>
@endsection
