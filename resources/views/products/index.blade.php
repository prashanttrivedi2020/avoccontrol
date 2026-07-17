@extends('layouts.app')

@section('title', __('Products') . ' – FireKontrol 365')
@section('page-title', __('Products'))

@section('topbar-actions')
 <image src={{ asset('storage/' . auth()->user()->logo_path) }} alt="Logo" style="height:41px;width:auto;">
    <a href="{{ route('products.import.upload') }}" class="btn btn-secondary">⬆ {{ __('Import CSV') }}</a>
    <a href="{{ route('products.import.failures') }}" class="btn btn-secondary">⚠ {{ __('Failed rows') }}</a>
    <a href="{{ route('products.create') }}" class="btn btn-primary">➕ {{ __('Add Product') }}</a>
@endsection

@section('content')
@if($products->isEmpty())
    <div class="empty-state">
        <div class="icon">📦</div>
        <h3>{{ __('No products created yet') }}</h3>
        <p style="margin-top:8px;margin-bottom:16px;font-size:14px;color:var(--text-muted)">
            {{ __('Create your products first to record loss data.') }}
        </p>
        <a href="{{ route('products.create') }}" class="btn btn-primary">➕ {{ __('Create first product') }}</a>
    </div>
@else
    <div class="card" style="padding:0">
        <div style="padding:16px 24px;border-bottom:1px solid rgba(255,255,255,0.08);display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap">
            <div>
                <h3 style="font-size:15px;font-weight:800;color:var(--white)">{{ __('Products') }}</h3>
                <p style="margin-top:4px;color:var(--text-muted);font-size:13px">{{ __('Showing :from–:to of :total products', ['from' => $products->firstItem(), 'to' => $products->lastItem(), 'total' => $products->total()]) }}</p>
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('Product name') }}</th>
                        <th>{{ __('Barcode') }}</th>
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('Supplier') }}</th>
                        <th>{{ __('Purchase price') }}</th>
                        <th>{{ __('Unit') }}</th>
                        <th>{{ __('Entries') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td>
                            <div style="font-weight:600">{{ $product->name }}</div>
                            @if(!$product->active)
                                <span class="badge badge-gray" style="font-size:10px">{{ __('inactive') }}</span>
                            @endif
                        </td>
                        <td style="color:var(--text-muted);font-family:monospace;font-size:12px">
                            {{ $product->barcode ?? '–' }}
                        </td>
                        <td>
                            @if($product->category)
                                <span class="badge badge-blue">{{ $product->category }}</span>
                            @else
                                <span style="color:var(--text-muted)">–</span>
                            @endif
                        </td>
                        <td style="color:var(--text-muted);font-size:13px">{{ $product->supplier ?? '–' }}</td>
                        <td style="font-weight:600">
                            @if($product->purchase_price)
                                {{ number_format($product->purchase_price, 2, ',', '.') }} €
                            @else
                                <span style="color:var(--text-muted)">–</span>
                            @endif
                        </td>
                        <td style="color:var(--text-muted)">{{ $product->unit }}</td>
                        <td>
                            <span class="badge badge-orange">{{ $product->losses_count }} {{ __('Entries') }}</span>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px">
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-secondary btn-sm">✏️</a>
                                <form method="POST" action="{{ route('products.destroy', $product) }}"
                                      onsubmit="return confirm('{{ __('Really delete product? All associated entries will also be deleted.') }}')">
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
    <div class="pagination" style="margin-top:20px">
        {{ $products->links() }}
    </div>
@endif
@endsection
