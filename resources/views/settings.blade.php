@extends('layouts.app')

@section('title', __('Settings') . ' – FireKontrol 365')
@section('page-title', __('Settings'))

@section('content')
<div class="card" style="max-width: 860px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;gap:12px;flex-wrap:wrap;">
        <div>
            <h2 style="font-size:20px;font-weight:800;margin-bottom:6px">{{ __('Company settings') }}</h2>
            <p style="color:var(--text-muted);font-size:14px">{{ __('Update your company profile that appears in your documents and exports.') }}</p>
        </div>
    </div>

    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" style="display:grid;gap:18px;">
        @csrf

        <div class="grid-2">
            <div>
                <label for="company_name" style="display:block;font-weight:600;margin-bottom:6px">{{ __('Company name') }}</label>
                <input id="company_name" name="company_name" type="text" value="{{ old('company_name', auth()->user()->company_name) }}" class="form-control" style="width:100%;padding:10px 12px;border:1px solid var(--border);border-radius:8px;">
            </div>
            <div>
                <label for="owner_name" style="display:block;font-weight:600;margin-bottom:6px">{{ __('Owner name') }}</label>
                <input id="owner_name" name="owner_name" type="text" value="{{ old('owner_name', auth()->user()->owner_name) }}" class="form-control" style="width:100%;padding:10px 12px;border:1px solid var(--border);border-radius:8px;">
            </div>
        </div>

        <div class="grid-2">
            <div>
                <label for="address" style="display:block;font-weight:600;margin-bottom:6px">{{ __('Address') }}</label>
                <textarea id="address" name="address" rows="3" class="form-control" style="width:100%;padding:10px 12px;border:1px solid var(--border);border-radius:8px;">{{ old('address', auth()->user()->address) }}</textarea>
            </div>
            <div>
                <label for="tax_number" style="display:block;font-weight:600;margin-bottom:6px">{{ __('Tax number') }}</label>
                <input id="tax_number" name="tax_number" type="text" value="{{ old('tax_number', auth()->user()->tax_number) }}" class="form-control" style="width:100%;padding:10px 12px;border:1px solid var(--border);border-radius:8px;">
            </div>
        </div>

        <div>
            <label for="logo" style="display:block;font-weight:600;margin-bottom:8px">{{ __('Company logo') }}</label>
            <input id="logo" name="logo" type="file" accept="image/*" style="width:100%;padding:10px 12px;border:1px solid var(--border);border-radius:8px;background:#fff;">
            @if(auth()->user()->logo_path)
                <div style="margin-top:10px;display:flex;align-items:center;gap:12px;">
                    <img src="{{ asset('storage/' . auth()->user()->logo_path) }}" alt="{{ __('Current company logo') }}" style="max-width:120px;max-height:80px;border-radius:8px;border:1px solid var(--border);">
                    <span style="color:var(--text-muted);font-size:13px">{{ __('Current logo') }}</span>
                </div>
            @endif
        </div>

        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <button type="submit" class="btn btn-primary">{{ __('Save settings') }}</button>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
        </div>
    </form>
</div>
@endsection
