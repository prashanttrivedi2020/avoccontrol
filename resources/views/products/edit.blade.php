@extends('layouts.app')

@section('title', __('Save changes') . ' – FireKontrol 365')
@section('page-title', __('Save changes'))

@section('topbar-actions')
    <a href="{{ route('products.index') }}" class="btn btn-secondary">{{ __('← Back') }}</a>
@endsection

@section('content')
<div style="max-width:640px">
    <div class="card">
        <form method="POST" action="{{ route('products.update', $product) }}">
            @csrf @method('PUT')

            <div class="form-group">
                <label class="form-label">{{ __('Product name *') }}</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" autofocus>
                @error('name') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div class="form-group">
                    <label class="form-label">{{ __('Barcode') }}</label>
                    <input type="text" name="barcode" class="form-control" value="{{ old('barcode', $product->barcode) }}" placeholder="EAN/UPC">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Unit *') }}</label>
                    <select name="unit" class="form-control">
                        @foreach(['Stk', 'kg', 'g', 'L', 'ml', 'Pkg', 'Bund', 'Kiste'] as $unit)
                            <option value="{{ $unit }}" {{ old('unit', $product->unit) === $unit ? 'selected' : '' }}>{{ $unit }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div class="form-group">
                    <label class="form-label">{{ __('Category') }}</label>
                    <input type="text" name="category" class="form-control" value="{{ old('category', $product->category) }}" list="categories" placeholder="{{ __('e.g. Dairy products') }}">
                    <datalist id="categories">
                        @foreach(['Backwaren', 'Fleisch', 'Fisch', 'Milchprodukte', 'Gemüse', 'Obst', 'Tiefkühl', 'Getränke', 'Wurst & Aufschnitt', 'Sonstiges'] as $cat)
                            <option value="{{ $cat }}">
                        @endforeach
                    </datalist>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Purchase price (€)') }}</label>
                    <input type="number" name="purchase_price" class="form-control" value="{{ old('purchase_price', $product->purchase_price) }}" step="0.01" min="0">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">{{ __('Supplier') }}</label>
                <input type="text" name="supplier" class="form-control" value="{{ old('supplier', $product->supplier) }}" placeholder="{{ __('e.g. Northern Dairy Ltd') }}">
            </div>

            <div class="form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;font-weight:500">
                    <input type="hidden" name="active" value="0">
                    <input type="checkbox" name="active" value="1" {{ old('active', $product->active) ? 'checked' : '' }}
                           style="width:16px;height:16px;accent-color:var(--green)">
                    {{ __('Product active') }}
                </label>
            </div>

            <div style="display:flex;gap:10px;margin-top:8px">
                <button type="submit" class="btn btn-primary">{{ __('Save changes') }}</button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
