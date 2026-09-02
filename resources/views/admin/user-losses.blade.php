@extends('layouts.app')

@section('title', __('User Loss Report'))
@section('page-title', __('User Loss Report'))

@section('content')
<div class="card" style="margin-bottom:18px;">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
        <div>
            <div style="font-size:12px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.08em;">{{ __('User') }}</div>
            <h3 style="font-size:24px;font-weight:700;">{{ $user->name }}</h3>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">{{ __('← Back to users') }}</a>
    </div>
</div>

<div class="card">
    @if($losses->isEmpty())
        <p style="color:var(--text-muted);">{{ __('No loss entries found for this user.') }}</p>
    @else
        <div style="display:grid;gap:12px;">
            @foreach($losses as $loss)
                @php
                    $cls = match($loss->reason) {
                        'diebstahl' => 'badge-red',
                        'tathergang' => 'badge-red',
                        'verderb' => 'badge-orange',
                        'ablauf' => 'badge-orange',
                        'beschaedigung' => 'badge-blue',
                        default => 'badge-gray',
                    };
                @endphp
                <div style="display:flex;gap:14px;padding:14px 16px;border:1px solid var(--border);border-radius:12px;background:rgba(255,255,255,0.04);align-items:flex-start;">
                    <div style="flex:1;display:flex;flex-direction:column;gap:8px;">
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;flex-wrap:wrap;">
                            <div>
                                <div style="font-weight:700;font-size:14px;">{{ $loss->product?->name ?? __('Unknown product') }}</div>
                                <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">{{ number_format($loss->quantity, 2, ',', '.') }} {{ $loss->unit }}</div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-weight:700;font-size:15px;color:var(--accent);">{{ number_format($loss->totalValue(), 2, ',', '.') }} €</div>
                            </div>
                        </div>
                        <div>
                            <span class="badge {{ $cls }}">{{ \App\Models\Loss::reasonLabel($loss->reason) }}</span>
                            <span style="font-size:11px;color:var(--text-muted);">{{ $loss->loss_date->format('d.m.Y') }}</span>
                        </div>
                        @if($loss->notes)
                            <div style="font-size:12px;color:var(--text-muted);">{{ $loss->notes }}</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
