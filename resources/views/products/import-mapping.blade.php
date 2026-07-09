@extends('layouts.app')

@section('title', __('Map columns') . ' – FireKontrol 365')
@section('page-title', __('Map columns'))

@section('topbar-actions')
    <a href="{{ route('products.import.upload') }}" class="btn btn-secondary">{{ __('← Back') }}</a>
@endsection

@section('content')
<div style="max-width:720px">
    <div class="alert alert-info">
        ℹ️ {{ __('Assign the CSV columns to the product fields. The') }}
        <strong>{{ __('Product name') }}</strong>
        {{ __('field is required.') }}
    </div>

    <div class="card">
        <form method="POST" action="{{ route('products.import.process') }}">
            @csrf

            {{-- Mapping table --}}
            <div style="border:1px solid var(--border);border-radius:10px;overflow:hidden;margin-bottom:20px">
                {{-- Header row --}}
                <div style="display:grid;grid-template-columns:1fr 1fr">
                    <div style="padding:10px 16px;background:var(--card2);font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;border-bottom:1px solid var(--border)">
                        {{ __('Product field') }}
                    </div>
                    <div style="padding:10px 16px;background:var(--card2);font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;border-bottom:1px solid var(--border)">
                        {{ __('CSV column') }}
                    </div>
                </div>

                @foreach($fields as $key => $field)
                @php
                    $label    = is_array($field) ? $field['label']    : $field;
                    $required = is_array($field) ? ($field['required'] ?? false) : false;
                    $autoIdx  = $autoMap[$key] ?? null;
                @endphp
                <div style="display:grid;grid-template-columns:1fr 1fr;border-bottom:1px solid var(--border)">
                    <div style="padding:12px 16px;display:flex;align-items:center;font-size:14px;font-weight:600">
                        {{ $label }}{{ $required ? ' *' : '' }}
                    </div>
                    <div style="padding:8px 12px">
                        <select name="map[{{ $key }}]" class="form-control" style="padding:6px 10px;font-size:13px">
                            <option value="">— {{ __('ignore') }} —</option>
                            @foreach($headers as $i => $col)
                                <option value="{{ $i }}" {{ $autoIdx === $i ? 'selected' : '' }}>
                                    {{ $col }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Preview --}}
            <div class="card card-sm" style="margin-bottom:20px;background:var(--bg2)">
                <p style="font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;margin-bottom:8px">
                    {{ __('Preview (first 3 rows)') }}
                </p>
                <div style="overflow-x:auto">
                    <table style="font-size:12px">
                        <thead>
                            <tr>
                                @foreach($headers as $h)
                                <th style="padding:6px 10px;font-size:10px">{{ $h }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(array_slice($preview, 0, 3) as $row)
                            <tr>
                                @foreach($headers as $i => $h)
                                <td style="padding:6px 10px;color:var(--text-muted)">{{ $row[$i] ?? '' }}</td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div style="display:flex;gap:10px">
                <button type="submit" class="btn btn-primary" onclick="return validateMapping(this.form)">
                    {{ __('Import now') }} ({{ $rowCount }} {{ __('rows') }})
                </button>
                <a href="{{ route('products.import.upload') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>

<script>
const TRANS = {
    nameRequired: @json(__('Please assign at least the "Product name" column.')),
};
function validateMapping(form) {
    const nameSelect = form.querySelector('select[name="map[name]"]');
    if (!nameSelect || !nameSelect.value) {
        alert(TRANS.nameRequired);
        return false;
    }
    return true;
}
</script>
@endsection
