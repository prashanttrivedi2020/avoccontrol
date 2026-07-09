<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index()
    {
        $products = Auth::user()->products()
            ->withCount('losses')
            ->orderBy('name')
            ->paginate(50);

        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'barcode'        => ['nullable', 'string', 'max:50'],
            'category'       => ['nullable', 'string', 'max:100'],
            'supplier'       => ['nullable', 'string', 'max:255'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'unit'           => ['required', 'string', 'max:20'],
        ], [
            'name.required' => __('Product name is required.'),
        ]);

        Auth::user()->products()->create($data);

        return redirect()->route('products.index')->with('success', __('Product successfully created.'));
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'barcode'        => ['nullable', 'string', 'max:50'],
            'category'       => ['nullable', 'string', 'max:100'],
            'supplier'       => ['nullable', 'string', 'max:255'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'unit'           => ['required', 'string', 'max:20'],
            'active'         => ['boolean'],
        ]);

        $product->update($data);

        return redirect()->route('products.index')->with('success', __('Product updated.'));
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);
        $product->delete();
        return redirect()->route('products.index')->with('success', __('Product deleted.'));
    }

    public function searchByBarcode(Request $request)
    {
        $barcode = $request->query('barcode');
        $product = Auth::user()->products()->where('barcode', $barcode)->first();

        if ($product) {
            return response()->json($product);
        }

        return response()->json(['error' => __('Product not found.')], 404);
    }
}
