@extends('layouts.app')

@section('title', __('Edit Unit'))
@section('page-title', __('Edit Unit'))

@section('topbar-actions')
 <image src={{ asset('storage/' . auth()->user()->logo_path) }} alt="Logo" style="height:41px;width:auto;">
    <a href="{{ route('units.index') }}" class="btn btn-secondary">{{ __('← Back') }}</a>
@endsection

@section('content')
<div class="card">
    <form method="POST" action="{{ route('units.update', $unit) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label class="form-label" for="name">{{ __('Unit Name') }} *</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $unit->name) }}" required>
            @error('name') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="is_active">{{ __('Active') }}</label>
            <select name="is_active" id="is_active" class="form-control">
                <option value="1" {{ old('is_active', $unit->is_active) ? 'selected' : '' }}>{{ __('Yes') }}</option>
                <option value="0" {{ old('is_active', $unit->is_active) ? '' : 'selected' }}>{{ __('No') }}</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">{{ __('Save Unit') }}</button>
    </form>
</div>
@endsection
