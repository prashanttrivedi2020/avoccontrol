@extends('layouts.app')

@section('title', __('All Entries') . ' – FireKontrol 365')
@section('page-title', __('All Entries'))

@section('topbar-actions')
    <a href="{{ route('losses.export') }}" class="btn btn-secondary">📥 {{ __('CSV Export') }}</a>
    <a href="{{ route('losses.create') }}" class="btn btn-primary">➕ {{ __('Record Loss') }}</a>
@endsection

@section('content')
{{-- Filter bar --}}
<div class="card card-sm" style="margin-bottom:16px">
    <form method="GET" action="{{ route('losses.index') }}" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
        <div>
            <label class="form-label" style="font-size:11px">{{ __('From') }}</label>
            <input type="date" name="from" class="form-control" value="{{ request('from') }}" style="padding:6px 10px;font-size:13px">
        </div>
        <div>
            <label class="form-label" style="font-size:11px">{{ __('To') }}</label>
            <input type="date" name="to" class="form-control" value="{{ request('to') }}" style="padding:6px 10px;font-size:13px">
        </div>
        <div>
            <label class="form-label" style="font-size:11px">{{ __('Reason') }}</label>
            <select name="reason" class="form-control" style="padding:6px 10px;font-size:13px">
                <option value="">{{ __('All') }}</option>
                @foreach(\App\Models\Loss::REASONS as $key)
                    <option value="{{ $key }}" {{ request('reason') === $key ? 'selected' : '' }}>
                        {{ \App\Models\Loss::reasonLabel($key) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label" style="font-size:11px">{{ __('Product') }}</label>
            
        </div>
        <div style="display:flex;gap:6px;padding-bottom:1px">
            <button type="submit" class="btn btn-primary btn-sm">🔍 {{ __('Filter') }}</button>
            <a href="{{ route('losses.index') }}" class="btn btn-secondary btn-sm">✕</a>
        </div>
    </form>
</div>

@if($losses->isEmpty())
    <div class="empty-state">
        <div class="icon">📭</div>
        <h3>{{ __('No entries found') }}</h3>
        <p style="margin-top:8px;margin-bottom:16px;font-size:14px;color:var(--text-muted)">
            {{ request()->hasAny(['from','to','reason','product_id'])
                ? __('No entries match the current filter.')
                : __('Record your first loss entry.') }}
        </p>
        <a href="{{ route('losses.create') }}" class="btn btn-primary">➕ {{ __('Record Loss') }}</a>
    </div>
@else
    <div class="card" style="padding:0;margin-bottom:16px">
        <div style="display:grid;gap:12px">
                    @foreach($losses as $loss)
                    @php
                        $cls = match($loss->reason) {
                            'diebstahl','tathergang' => 'badge-red',
                            'verderb','ablauf'      => 'badge-orange',
                            'beschaedigung'         => 'badge-blue',
                            default                 => 'badge-gray',
                        };
                    @endphp
                    <div style="display:flex;gap:14px;padding:14px 16px;border:1px solid rgba(255,255,255,0.08);border-radius:12px;background:rgba(255,255,255,0.04);align-items:flex-start">
                        <!-- Image -->
                        <div style="flex-shrink:0;width:90px;height:90px;border-radius:8px;overflow:hidden;background:rgba(255,255,255,0.08)">
                            @if($loss->photo_path)
                                <img src="{{ asset('storage/' . $loss->photo_path) }}" alt="Loss Photo" style="width:100%;height:auto;object-fit:cover">
                            @else
                                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:var(--text-muted);font-size:24px">📦</div>
                            @endif
                        </div>
                        
                        <!-- Content -->
                        <div style="flex:1;display:flex;flex-direction:column;gap:8px">
                            <div style="display:flex;justify-content:space-between;align-items:flex-start">
                                <div>
                                    <a href="{{ route('losses.show', $loss) }}" style="font-weight:700;font-size:14px;text-decoration:none">{{ $loss->product->name }}</a>
                                    <div style="font-size:12px;color:var(--text-muted);margin-top:2px">{{ number_format($loss->quantity, 2, ',', '.') }} {{ $loss->unit }}</div>
                                    @if($loss->note)
                                        <div style="font-size:11px;color:var(--text-muted);margin-top:4px;max-width:300px">{{ Str::limit($loss->note, 80) }}</div>
                                    @endif
                                </div>
                                <div style="text-align:right;flex-shrink:0">
                                    
                                    <div style="font-weight:700;font-size:15px;color:var(--accent);margin-top:2px">{{ number_format($loss->totalValue(), 2, ',', '.') }} €</div>
                                </div>
                            </div>
                            <div style="display:flex;justify-content:space-between;align-items:center">
                                <span class="badge {{ $cls }}">{{ \App\Models\Loss::reasonLabel($loss->reason) }}</span> <span style="font-size:11px;color:var(--text-muted)">{{ $loss->loss_date->format('d.m.Y') }}</span>
                                <div style="display:flex;gap:6px">
                                    <a href="{{ route('losses.show', $loss) }}" class="btn btn-secondary btn-sm" title="{{ __('View') }}">👁</a>
                                    <a href="{{ route('losses.edit', $loss) }}" class="btn btn-primary btn-sm" title="{{ __('Edit') }}">✏️</a>
                                    <form method="POST" action="{{ route('losses.destroy', $loss) }}" style="display:inline" onsubmit="return confirm('{{ __('Really delete this entry? This cannot be undone.') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="{{ __('Delete') }}">🗑</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
    </div>

    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px">
        <div style="font-size:13px;color:var(--text-muted)">
            {{ $losses->total() }} {{ __('Entries') }} · {{ __('Total value') }}: <strong style="color:var(--accent)">
                {{ number_format($losses->sum(fn($l) => $l->totalValue()), 2, ',', '.') }} €
            </strong>
        </div>
        <div class="pagination">
            {{ $losses->appends(request()->query())->links() }}
        </div>
    </div>
@endif
@endsection
