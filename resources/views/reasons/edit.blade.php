@extends('layouts.app')

@section('title', __('Edit Reason'))
@section('page-title', __('Edit Reason'))

@section('topbar-actions')
    <a href="{{ route('reasons.index') }}" class="btn btn-secondary">{{ __('← Back') }}</a>
@endsection

@section('content')
<div class="card">
    <form method="POST" action="{{ route('reasons.update', $reason) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label class="form-label" for="name">{{ __('Reason Name') }} *</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $reason->name) }}" required>
            @error('name') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="is_active">{{ __('Active') }}</label>
            <select name="is_active" id="is_active" class="form-control">
                <option value="1" {{ old('is_active', $reason->is_active) ? 'selected' : '' }}>{{ __('Yes') }}</option>
                <option value="0" {{ old('is_active', $reason->is_active) ? '' : 'selected' }}>{{ __('No') }}</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">{{ __('Save Reason') }}</button>
    </form>
</div>
@endsection
