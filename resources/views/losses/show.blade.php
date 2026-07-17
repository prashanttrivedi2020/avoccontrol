@extends('layouts.app')

@section('title', __('Loss Entry') . ' – FireKontrol 365')
@section('page-title', __('Loss Entry'))

@section('topbar-actions')
 <image src={{ asset('storage/' . auth()->user()->logo_path) }} alt="Logo" style="height:41px;width:auto;">
    <a href="{{ route('losses.index') }}" class="btn btn-secondary">{{ __('← Back') }}</a>
@endsection

@section('content')
<div style="max-width:720px">
    <div class="card" style="margin-bottom:16px">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">
            {{-- Left column --}}
            <div>
                <div style="margin-bottom:20px">
                    <div class="stat-label">{{ __('Product') }}</div>
                    <div style="font-size:20px;font-weight:800;margin-top:4px">{{ $loss->product->name }}</div>
                    @if($loss->product->barcode)
                        <div style="font-size:12px;color:var(--text-muted);margin-top:4px;font-family:monospace">{{ $loss->product->barcode }}</div>
                    @endif
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
                    <div>
                        <div class="stat-label">{{ __('Date') }}</div>
                        <div style="font-size:16px;font-weight:700;margin-top:4px">{{ $loss->loss_date->format('d.m.Y') }}</div>
                    </div>
                    <div>
                        <div class="stat-label">{{ __('Quantity') }}</div>
                        <div style="font-size:16px;font-weight:700;margin-top:4px">
                            {{ number_format($loss->quantity, 2, ',', '.') }} {{ $loss->unit }}
                        </div>
                    </div>
                </div>

                <div style="margin-bottom:20px">
                    <div class="stat-label">{{ __('Reason') }}</div>
                    <div style="margin-top:6px">
                        @php
                            $cls = match($loss->reason) {
                                'diebstahl','tathergang' => 'badge-red',
                                'verderb','ablauf'      => 'badge-orange',
                                'beschaedigung'         => 'badge-blue',
                                default                 => 'badge-gray',
                            };
                        @endphp
                        <span class="badge {{ $cls }}" style="font-size:14px;padding:4px 12px">
                            {{ \App\Models\Loss::reasonLabel($loss->reason) }}
                        </span>
                    </div>
                </div>

                @if($loss->note)
                <div style="margin-bottom:20px">
                    <div class="stat-label">{{ __('Note') }}</div>
                    <div style="font-size:14px;line-height:1.6;margin-top:4px;color:var(--text)">{{ $loss->note }}</div>
                </div>
                @endif
            </div>

            {{-- Right column --}}
            <div>
                <div style="margin-bottom:20px">
                    <div class="stat-label">{{ __('Loss value') }}</div>
                    <div style="font-size:28px;font-weight:800;color:var(--accent);margin-top:4px">
                        {{ number_format($loss->totalValue(), 2, ',', '.') }} €
                    </div>
                    @if($loss->product->purchase_price)
                        <div style="font-size:12px;color:var(--text-muted);margin-top:2px">
                            {{ number_format($loss->product->purchase_price, 2, ',', '.') }} € × {{ number_format($loss->quantity, 2, ',', '.') }}
                        </div>
                    @endif
                </div>

                @if($loss->photo_path)
                <div style="margin-bottom:20px">
                    <div class="stat-label" style="margin-bottom:8px">{{ __('Photo') }}</div>
                    <a href="{{ asset('storage/' . $loss->photo_path) }}" target="_blank">
                        <img src="{{ asset('storage/' . $loss->photo_path) }}" alt="{{ __('Loss photo') }}"
                             style="width:100%;max-height:200px;object-fit:cover;border-radius:8px;border:1px solid var(--border)">
                    </a>
                </div>
                @endif

                <div>
                    <div class="stat-label">{{ __('Recorded at') }}</div>
                    <div style="font-size:13px;color:var(--text-muted);margin-top:4px">
                        {{ $loss->created_at->format('d.m.Y H:i') }} {{ __('by') }} {{ $loss->user->username ?? '–' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- GoBD hash --}}
    <div class="card card-sm" style="margin-bottom:16px;background:rgba(45,122,45,0.06);border-color:rgba(45,122,45,0.3)">
        <div style="display:flex;align-items:flex-start;gap:10px">
            <span style="font-size:18px;flex-shrink:0">🔒</span>
            <div>
                <div style="font-size:12px;font-weight:700;color:var(--green-l);margin-bottom:4px">
                    {{ __('GoBD tamper-proof hash') }}
                </div>
                <div style="font-family:monospace;font-size:11px;color:var(--text-muted);word-break:break-all;line-height:1.6">
                    {{ $loss->immutable_hash }}
                </div>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div style="display:flex;gap:10px">
        <a href="{{ route('losses.index') }}" class="btn btn-secondary">{{ __('← All Entries') }}</a>
        <form method="POST" action="{{ route('losses.destroy', $loss) }}"
              onsubmit="return confirm('{{ __('Really delete this entry? This cannot be undone.') }}')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger">🗑 {{ __('Delete entry') }}</button>
        </form>
    </div>
</div>
@endsection
