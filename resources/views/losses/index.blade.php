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
            <select name="product_id" class="form-control" style="padding:6px 10px;font-size:13px">
                <option value="">{{ __('All') }}</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
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
                        <th>{{ __('Note') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($losses as $loss)
                    <tr>
                        <td style="white-space:nowrap">
                            <a href="{{ route('losses.show', $loss) }}" style="color:var(--text-muted);text-decoration:none;font-size:13px">
                                {{ $loss->loss_date->format('d.m.Y') }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('losses.show', $loss) }}" style="font-weight:600;text-decoration:none;color:var(--text)">
                                {{ $loss->product->name }}
                            </a>
                        </td>
                        <td style="white-space:nowrap">
                            {{ number_format($loss->quantity, 2, ',', '.') }} {{ $loss->unit }}
                        </td>
                        <td>
                            @php
                                $cls = match($loss->reason) {
                                    'diebstahl','tathergang' => 'badge-red',
                                    'verderb','ablauf'      => 'badge-orange',
                                    'beschaedigung'         => 'badge-blue',
                                    default                 => 'badge-gray',
                                };
                            @endphp
                            <span class="badge {{ $cls }}">{{ \App\Models\Loss::reasonLabel($loss->reason) }}</span>
                        </td>
                        <td style="color:var(--accent);font-weight:700;white-space:nowrap">
                            {{ number_format($loss->totalValue(), 2, ',', '.') }} €
                        </td>
                        <td>
                            @if($loss->photo_path)
                                <a href="{{ asset('storage/' . $loss->photo_path) }}" target="_blank" style="font-size:18px;text-decoration:none">📷</a>
                            @else
                                <span style="color:var(--text-muted)">–</span>
                            @endif
                        </td>
                        <td style="max-width:180px">
                            @if($loss->note)
                                <span style="font-size:12px;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block">
                                    {{ Str::limit($loss->note, 40) }}
                                </span>
                            @else
                                <span style="color:var(--text-muted)">–</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:6px">
                                <a href="{{ route('losses.show', $loss) }}" class="btn btn-secondary btn-sm">👁</a>
                                <form method="POST" action="{{ route('losses.destroy', $loss) }}"
                                      onsubmit="return confirm('{{ __('Really delete this entry? This cannot be undone.') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">🗑</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px">
        <div style="font-size:13px;color:var(--text-muted)">
            {{ $losses->total() }} {{ __('Entries') }} · {{ __('Total value') }}: <strong style="color:var(--accent)">{{ number_format($totalValue, 2, ',', '.') }} €</strong>
        </div>
        <div class="pagination">
            {{ $losses->appends(request()->query())->links() }}
        </div>
    </div>
@endif
@endsection
