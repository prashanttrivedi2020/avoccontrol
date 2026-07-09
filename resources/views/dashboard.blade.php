@extends('layouts.app')

@section('title', __('Dashboard') . ' – FireKontrol 365')
@section('page-title', __('Dashboard'))

@section('topbar-actions')
    <a href="{{ route('losses.create') }}" class="btn btn-primary">➕ {{ __('Record Loss') }}</a>
@endsection

@section('content')
<div class="grid-4" style="margin-bottom:24px">
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

<div class="grid-2" style="margin-bottom:24px">
    <!-- Top Products -->
    <div class="card">
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
    </div>

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

<!-- Latest Entries -->
<div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
        <h3 style="font-size:15px;font-weight:800;color:var(--white)">📋 {{ __('Latest Entries') }}</h3>
        <a href="{{ route('losses.index') }}" style="font-size:13px;color:var(--accent2);text-decoration:none;font-weight:700">{{ __('Show all →') }}</a>
    </div>
    @if($recentLosses->isEmpty())
        <div class="empty-state" style="padding:30px 0">
            <div class="icon">📭</div>
            <h3>{{ __('No loss data yet') }}</h3>
            <p style="margin-top:8px;margin-bottom:16px;font-size:14px">{{ __('Record your first loss entry.') }}</p>
            <a href="{{ route('losses.create') }}" class="btn btn-primary">➕ {{ __('Record Loss') }}</a>
        </div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Product') }}</th>
                        <th>{{ __('Quantity') }}</th>
                        <th>{{ __('Reason') }}</th>
                        <th>{{ __('Value') }}</th>
                        <th>{{ __('Photo') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentLosses as $loss)
                    <tr>
                        <td style="color:var(--text-muted)">{{ $loss->loss_date->format('d.m.Y') }}</td>
                        <td style="font-weight:600">{{ $loss->product->name }}</td>
                        <td>{{ number_format($loss->quantity, 2, ',', '.') }} {{ $loss->unit }}</td>
                        <td>
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
                            <span class="badge {{ $cls }}">{{ \App\Models\Loss::reasonLabel($loss->reason) }}</span>
                        </td>
                        <td style="color:var(--accent);font-weight:700">
                            {{ number_format($loss->totalValue(), 2, ',', '.') }} €
                        </td>
                        <td>
                            @if($loss->photo_path)
                                <a href="{{ asset('storage/' . $loss->photo_path) }}" target="_blank"
                                   style="font-size:18px;text-decoration:none">📷</a>
                            @else
                                <span style="color:var(--text-muted)">–</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
