@extends('layouts.app')

@section('title', __('Failed import rows') . ' – FireKontrol 365')
@section('page-title', __('Failed import rows'))

@section('topbar-actions')
    <a href="{{ route('products.import.upload') }}" class="btn btn-secondary">⬆ {{ __('Import CSV') }}</a>
    <a href="{{ route('products.index') }}" class="btn btn-secondary">📦 {{ __('Products') }}</a>
@endsection

@section('content')
<div class="card" style="padding:0">
    <div style="padding:20px 24px;border-bottom:1px solid rgba(255,255,255,0.08)">
        <h3 style="font-size:15px;font-weight:800;color:var(--white)">{{ __('Review failed import rows') }}</h3>
        <p style="margin-top:6px;color:var(--text-muted);font-size:13px">{{ __('Rows that could not be saved are listed here so you can fix them and try again.') }}</p>
    </div>

    @if($failures->isEmpty())
        <div class="empty-state" style="padding:40px 20px">
            <div class="icon">✅</div>
            <h3>{{ __('No failed rows') }}</h3>
            <p style="margin-top:8px;font-size:14px;color:var(--text-muted)">{{ __('All recent imports were processed successfully.') }}</p>
        </div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Row') }}</th>
                        <th>{{ __('Data') }}</th>
                        <th>{{ __('Error') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($failures as $failure)
                        <tr>
                            <td style="color:var(--text-muted);white-space:nowrap">{{ $failure->created_at->format('d.m.Y H:i') }}</td>
                            <td style="font-weight:600">{{ $failure->row_number }}</td>
                            <td style="max-width:280px">
                                @php $rowData = json_decode($failure->row_data, true) ?: []; @endphp
                                @if($rowData)
                                    <div style="display:grid;gap:2px">
                                        @foreach($rowData as $key => $value)
                                            <div style="font-size:12px;color:var(--text-muted)"><span style="color:var(--white);font-weight:600">{{ $key }}:</span> {{ $value ?: '–' }}</div>
                                        @endforeach
                                    </div>
                                @else
                                    <span style="color:var(--text-muted)">–</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-red" style="max-width:100%">{{ $failure->error_message }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
