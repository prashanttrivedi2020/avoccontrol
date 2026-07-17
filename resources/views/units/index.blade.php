@extends('layouts.app')

@section('title', __('Units'))
@section('page-title', __('Units'))

@section('topbar-actions')
 <image src={{ asset('storage/' . auth()->user()->logo_path) }} alt="Logo" style="height:41px;width:auto;">
    <a href="{{ route('units.create') }}" class="btn btn-primary">{{ __('Add Unit') }}</a>
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
                @forelse($units as $unit)
                    <tr>
                        <td>{{ $unit->name }}</td>
                        <td>{{ $unit->trashed() ? __('Deleted') : ($unit->is_active ? __('Active') : __('Inactive')) }}</td>
                        <td>
                            @unless($unit->trashed())
                                <a href="{{ route('units.edit', $unit) }}" class="btn btn-secondary btn-sm">{{ __('Edit') }}</a>
                            @endunless
                            <form action="{{ route('units.destroy', $unit) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('Are you sure?') }}')">{{ $unit->trashed() ? __('Delete') : __('Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-muted">{{ __('No units found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
