@extends('layouts.app')

@section('title', __('Reasons'))
@section('page-title', __('Reasons'))

@section('topbar-actions')
 <image src={{ asset('storage/' . auth()->user()->logo_path) }} alt="Logo" style="height:41px;width:auto;">
    <a href="{{ route('reasons.create') }}" class="btn btn-primary">{{ __('Add Reason') }}</a>
    <a href="{{ route('dashboard') }}" class="btn btn-secondary">{{ __('← Back') }}</a>
@endsection

@section('content')
<div class="card">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reasons as $reason)
                    <tr>
                        <td>{{ $reason->name }}</td>
                        <td>{{ $reason->trashed() ? __('Deleted') : ($reason->is_active ? __('Active') : __('Inactive')) }}</td>
                        <td>
                            @unless($reason->trashed())
                                <a href="{{ route('reasons.edit', $reason) }}" class="btn btn-secondary btn-sm">{{ __('Edit') }}</a>
                            @endunless
                            <form action="{{ route('reasons.destroy', $reason) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('Are you sure?') }}')">{{ __('Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-muted">{{ __('No reasons found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
