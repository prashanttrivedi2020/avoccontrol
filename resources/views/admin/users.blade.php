@extends('layouts.app')

@section('title', __('Admin Users'))
@section('page-title', __('Admin Users'))

@section('content')
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;gap:12px;flex-wrap:wrap;">
        <h3 style="font-size:18px;font-weight:700;">{{ __('Registered users') }}</h3>
    </div>

    @if($users->isEmpty())
        <p style="color:var(--text-muted);">{{ __('No users found.') }}</p>
    @else
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;min-width:800px;">
                <thead>
                    <tr style="border-bottom:1px solid var(--border);text-align:left;">
                        <th style="padding:12px 10px;">{{ __('Name') }}</th>
                        <th style="padding:12px 10px;">{{ __('Email') }}</th>
                        <th style="padding:12px 10px;">{{ __('Role') }}</th>
                        <th style="padding:12px 10px;">{{ __('Status') }}</th>
                        <th style="padding:12px 10px;">{{ __('Losses') }}</th>
                        <th style="padding:12px 10px;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr style="border-bottom:1px solid var(--border);">
                            <td style="padding:12px 10px;">{{ $user->name }}</td>
                            <td style="padding:12px 10px;">{{ $user->email }}</td>
                            <td style="padding:12px 10px;">
                                <span class="badge {{ $user->role === 'admin' ? 'badge-blue' : 'badge-gray' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td style="padding:12px 10px;">
                                <span class="badge {{ $user->is_active ? 'badge-green' : 'badge-red' }}">
                                    {{ $user->is_active ? __('Active') : __('Inactive') }}
                                </span>
                            </td>
                            <td style="padding:12px 10px;">{{ $user->losses()->count() }}</td>
                            <td style="padding:12px 10px;display:flex;gap:8px;flex-wrap:wrap;">
                                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                                    @csrf
                                    <button type="submit" class="btn {{ $user->is_active ? 'btn-secondary' : 'btn-primary' }} btn-sm">
                                        {{ $user->is_active ? __('Deactivate') : __('Activate') }}
                                    </button>
                                </form>
                                <a href="{{ route('admin.users.losses', $user) }}" class="btn btn-secondary btn-sm">{{ __('View Report') }}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
