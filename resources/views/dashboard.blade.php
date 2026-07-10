@extends('layouts.app')

@section('title', __('Dashboard') . ' – FireKontrol 365')
@section('page-title', __('Dashboard'))

@section('topbar-actions')
    <a href="{{ route('losses.create') }}" class="btn btn-primary" style="display: none;">➕ {{ __('Record Loss') }}</a>
@endsection

@section('content')
<div class="grid-4" style="margin-bottom:6px;">
    <div class="stat-card">
        <div class="stat-label">{{ __('This Month') }}</div>
        <div class="stat-value">{{ number_format($thisMonthLosses) }}</div>
        <div class="stat-sub">{{ __('Entries in current month') }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">{{ __('Monthly Value') }}</div>
        <div class="stat-value">{{ number_format($thisMonthValue, 2, ',', '.') }} €</div>
        <div class="stat-sub">{{ __('Purchase cost this month') }}</div>
    </div>
</div>



<!-- Latest Entries -->
<div class="card">
    
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
        <h3 style="font-size:15px;font-weight:800;color:var(--white)">📋 {{ __('Latest Entries') }}</h3>
        <div><a href="{{ route('losses.index') }}" style="font-size:13px;color:var(--accent2);text-decoration:none;font-weight:700">{{ __('Show all →') }}</a></div>
        <div style="position: absolute;"><a href="{{ route('losses.create') }}" class="btn btn-primary">➕ {{ __('Record Loss') }}</a>
         <a href="{{ route('products.create') }}" class="btn btn-primary {{ request()->routeIs('products.create') ? 'active' : '' }}">
            <span class="icon">➕</span> {{ __('Add Product') }}
        </a>
    </div>
       
    </div>
    @if($recentLosses->isEmpty())
        <div class="empty-state" style="padding:30px 0">
            <div class="icon">📭</div>
            <h3>{{ __('No loss data yet') }}</h3>
            <p style="margin-top:8px;margin-bottom:16px;font-size:14px">{{ __('Record your first loss entry.') }}</p>
            <a href="{{ route('losses.create') }}" class="btn btn-primary">➕ {{ __('Record Loss') }}</a>
            <a href="{{ route('products.create') }}" class="btn btn-primary {{ request()->routeIs('products.create') ? 'active' : '' }}">
            <span class="icon">➕</span> {{ __('Add Product') }}</a>
        </div>
    @else
    
        <div style="display:grid;gap:12px">
            @foreach($recentLosses as $loss)
                @php
                    $cls = match($loss->reason) {
                        'diebstahl' => 'badge-red',
                        'tathergang' => 'badge-red',
                        'verderb'   => 'badge-orange',
                        'ablauf'    => 'badge-orange',
                        'beschaedigung' => 'badge-blue',
                        default     => 'badge-gray',
                    };
                @endphp
                <div style="display:flex;gap:14px;padding:14px 16px;border: 2px dashed red;border-radius:12px;background:rgba(255,255,255,0.04);align-items:flex-start">
                    <!-- Image -->
                    <div style="flex-shrink:0;width:90px;height:90px;border-radius:8px;overflow:hidden;background:rgba(255,255,255,0.08)">
                        @if($loss->photo_path)
                            <img src="{{ Storage::url($loss->photo_path) }}" alt="Loss Photo" style="width:100%;height:auto;object-fit:cover">
                        @else
                            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:var(--text-muted);font-size:24px">📦</div>
                        @endif
                    </div>
                    
                    <!-- Content -->
                    <div style="flex:1;display:flex;flex-direction:column;gap:8px">
                        <div style="display:flex;justify-content:space-between;align-items:flex-start">
                            <div>
                                <div style="font-weight:700;font-size:14px;">{{ $loss->product->name }}</div>
                                <div style="font-size:12px;color:var(--text-muted);margin-top:2px">{{ number_format($loss->quantity, 2, ',', '.') }} {{ $loss->unit }}</div>
                            </div>
                            <div style="text-align:right">
                               
                                <div style="font-weight:700;font-size:15px;color:var(--accent);margin-top:2px">{{ number_format($loss->totalValue(), 2, ',', '.') }} €</div>
                            </div>
                        </div>
                        <div>
                            <span class="badge {{ $cls }}">{{ \App\Models\Loss::reasonLabel($loss->reason) }} </span> <span style="font-size:11px;color:var(--text-muted)">{{ $loss->loss_date->format('d.m.Y') }}</span>
                        </div>
                        <span><a href="{{ route('losses.edit', $loss) }}" class="btn btn-primary btn-sm" title="{{ __('Edit') }}">✏️</a></span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
<div class="grid-2" style="margin-bottom:6px">
    <!-- Top Products -->
    <!-- <div class="card">
        <h3 style="font-size:15px;font-weight:700;margin-bottom:16px;color:var(--white)">
            📊 {{ __('Top discarded products') }}
        </h3>
        @if($topProducts->isEmpty())
            <p style="color:var(--text-muted);font-size:14px">{{ __('No data yet.') }}</p>
        @else
            @foreach($topProducts as $i => $tp)
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">
                <div style="width:24px;height:24px;background:var(--card2);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:var(--text-muted);flex-shrink:0">
                    {{ $i + 1 }}
                </div>
                <div style="flex:1;min-width:0">
                    <div style="font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                        {{ $tp->product->name ?? '–' }}
                    </div>
                    <div style="font-size:11px;color:var(--text-muted)">
                        {{ number_format($tp->total_qty, 1, ',', '.') }} {{ __('units') }}
                    </div>
                </div>
                <div style="font-size:13px;color:var(--accent);font-weight:700;flex-shrink:0">
                    {{ number_format($tp->total_value ?? 0, 2, ',', '.') }} €
                </div>
            </div>
            @endforeach
        @endif
    </div> -->

    <!-- By Reason -->
    <div class="card">
        <h3 style="font-size:15px;font-weight:700;margin-bottom:16px;color:var(--white)">
            🗂️ {{ __('Losses by reason') }}
        </h3>
        @if($byReason->isEmpty())
            <p style="color:var(--text-muted);font-size:14px">{{ __('No data yet.') }}</p>
        @else
            @foreach($byReason as $br)
            @php
                $pct = $totalLosses > 0 ? ($br->count / $totalLosses) * 100 : 0;
            @endphp
            <div style="margin-bottom:14px">
                <div style="display:flex;justify-content:space-between;margin-bottom:5px">
                    <span style="font-size:13px;font-weight:600">
                        {{ \App\Models\Loss::reasonLabel($br->reason) }}
                    </span>
                    <span style="font-size:12px;color:var(--text-muted)">
                        {{ $br->count }} ({{ number_format($pct, 0) }}%)
                    </span>
                </div>
                <div style="background:rgba(255,255,255,0.08);border-radius:4px;height:6px;overflow:hidden">
                    <div style="background:var(--accent2);height:100%;width:{{ min($pct, 100) }}%;border-radius:4px;transition:width .3s"></div>
                </div>
            </div>
            @endforeach
        @endif
    </div>
</div>
<!-- <div class="grid-4" style="margin-bottom:6px">
    <div class="stat-card">
        <div class="stat-label">{{ __('Total Entries') }}</div>
        <div class="stat-value">{{ number_format($totalLosses) }}</div>
        <div class="stat-sub">{{ __('All documented losses') }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">{{ __('Total Loss Value') }}</div>
        <div class="stat-value accent">{{ number_format($totalLossValue, 2, ',', '.') }} €</div>
        <div class="stat-sub">{{ __('Purchase cost of all losses') }}</div>
    </div>
    
</div> -->
@endsection
