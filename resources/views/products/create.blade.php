@extends('layouts.app')

@section('title', __('Add Product') . ' – FireKontrol 365')
@section('page-title', __('Add Product'))

@section('topbar-actions')
 <image src={{ asset('storage/' . auth()->user()->logo_path) }} alt="Logo" style="height:41px;width:auto;">
    <a href="{{ route('products.index') }}" class="btn btn-secondary">{{ __('← Back') }}</a>
@endsection

@section('content')
<div style="max-width:640px">
    <div class="card">
        <form method="POST" action="{{ route('products.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">{{ __('Product name *') }}</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                       placeholder="{{ __('e.g. Whole milk 3.5%') }}" autofocus>
                @error('name') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div class="form-group">
                    <label class="form-label">{{ __('Barcode') }}</label>
                    <div style="display:flex;gap:8px">
                        <input type="text" name="barcode" id="barcode-input" class="form-control"
                               value="{{ old('barcode') }}" placeholder="EAN/UPC">
                    </div>
                    <div class="form-hint">{{ __('EAN-8, EAN-13 or UPC') }}</div>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Unit *') }}</label>
                    <select name="unit" class="form-control">
                        @foreach(['Stk', 'kg', 'g', 'L', 'ml', 'Pkg', 'Bund', 'Kiste'] as $unit)
                            <option value="{{ $unit }}" {{ old('unit', 'Stk') === $unit ? 'selected' : '' }}>
                                {{ $unit }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div class="form-group">
                    <label class="form-label">{{ __('Category') }}</label>
                    <input type="text" name="category" class="form-control" value="{{ old('category') }}"
                           list="categories" placeholder="{{ __('e.g. Dairy products') }}">
                    <datalist id="categories">
                        @foreach(['Backwaren', 'Fleisch', 'Fisch', 'Milchprodukte', 'Gemüse', 'Obst', 'Tiefkühl', 'Getränke', 'Wurst & Aufschnitt', 'Sonstiges'] as $cat)
                            <option value="{{ $cat }}">
                        @endforeach
                    </datalist>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Purchase price (€)') }}</label>
                    <input type="number" name="purchase_price" class="form-control"
                           value="{{ old('purchase_price') }}" step="0.01" min="0" placeholder="1.29">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">{{ __('Supplier') }}</label>
                <input type="text" name="supplier" class="form-control" value="{{ old('supplier') }}"
                       placeholder="{{ __('e.g. Northern Dairy Ltd') }}">
            </div>

            <div style="display:flex;gap:10px;margin-top:8px">
                <button type="submit" class="btn btn-primary">{{ __('Save product') }}</button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
