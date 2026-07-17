@extends('layouts.app')

@section('title', __('Add Reason'))
@section('page-title', __('Add Reason'))

@section('topbar-actions')
 <image src={{ asset('storage/' . auth()->user()->logo_path) }} alt="Logo" style="height:41px;width:auto;">
    <a href="{{ route('reasons.index') }}" class="btn btn-secondary">{{ __('← Back') }}</a>
@endsection

@section('content')
<div class="card">
    <form method="POST" action="{{ route('reasons.store') }}">
        @csrf
        <div class="form-group">
            <label class="form-label" for="name">{{ __('Reason Name') }} *</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
            @error('name') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <button type="submit" class="btn btn-primary">{{ __('Save Reason') }}</button>
    </form>
</div>
@endsection
