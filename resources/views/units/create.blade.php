@extends('layouts.app')

@section('title', __('Add Unit'))
@section('page-title', __('Add Unit'))

@section('topbar-actions')
    <a href="{{ route('units.index') }}" class="btn btn-secondary">{{ __('← Back') }}</a>
@endsection

@section('content')
<div class="card">
    <form method="POST" action="{{ route('units.store') }}">
        @csrf
        <div class="form-group">
            <label class="form-label" for="name">{{ __('Unit Name') }} *</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
            @error('name') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <button type="submit" class="btn btn-primary">{{ __('Save Unit') }}</button>
    </form>
</div>
@endsection
