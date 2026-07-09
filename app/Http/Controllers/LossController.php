<?php

namespace App\Http\Controllers;

use App\Models\Loss;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LossController extends Controller
{
    public function index(Request $request)
    {
        $query = Auth::user()->losses()->with('product')->orderByDesc('loss_date');

        if ($request->filled('reason')) {
            $query->where('reason', $request->reason);
        }
        if ($request->filled('from')) {
            $query->whereDate('loss_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('loss_date', '<=', $request->to);
        }
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $losses   = $query->paginate(20)->withQueryString();
        $products = Auth::user()->products()->orderBy('name')->get();

        return view('losses.index', compact('losses', 'products'));
    }

    public function create()
    {
        $products = Auth::user()->products()->where('active', true)->orderBy('name')->get();
        return view('losses.create', compact('products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id'     => ['required', 'exists:products,id'],
            'loss_date'      => ['required', 'date', 'before_or_equal:today'],
            'quantity'       => ['required', 'numeric', 'min:0.001'],
            'unit'           => ['required', 'string', 'max:20'],
            'reason'         => ['required', 'string', 'in:verderb,ablauf,diebstahl,beschaedigung,tathergang,sonstiges'],
            'supplier'       => ['nullable', 'string', 'max:255'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'photo'          => ['nullable', 'image', 'max:10240'],
            'photo_data'     => ['nullable', 'string'],
            'notes'          => ['nullable', 'string', 'max:1000'],
        ], [
            'product_id.required'  => __('Please select a product.'),
            'loss_date.required'   => __('Date is required.'),
            'quantity.required'    => __('Quantity is required.'),
            'reason.required'      => __('Reason is required.'),
        ]);

        // Ensure this product belongs to the user
        $product = Auth::user()->products()->findOrFail($data['product_id']);

        // Camera-captured photo (base64 data URL)
        if ($request->filled('photo_data')) {
            $dataUrl = $request->input('photo_data');
            if (preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $m)) {
                $ext       = $m[1] === 'png' ? 'png' : 'jpg';
                $imageData = base64_decode(substr($dataUrl, strpos($dataUrl, ',') + 1));
                $filename  = 'loss-photos/' . Str::uuid() . '.' . $ext;
                Storage::disk('public')->put($filename, $imageData);
                $data['photo_path'] = $filename;
            }
        } elseif ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('loss-photos', 'public');
            $data['photo_path'] = $path;
        }
        unset($data['photo_data']);

        // Pre-fill supplier/price from product if not set
        if (empty($data['supplier'])) {
            $data['supplier'] = $product->supplier;
        }
        if (empty($data['purchase_price'])) {
            $data['purchase_price'] = $product->purchase_price;
        }

        $data['user_id'] = Auth::id();

        Loss::create($data);

        return redirect()->route('losses.index')->with('success', __('Loss successfully documented.'));
    }

    public function show(Loss $loss)
    {
        $this->authorize('view', $loss);
        return view('losses.show', compact('loss'));
    }

    public function destroy(Loss $loss)
    {
        $this->authorize('delete', $loss);

        if ($loss->photo_path) {
            Storage::disk('public')->delete($loss->photo_path);
        }

        $loss->delete();

        return redirect()->route('losses.index')->with('success', __('Entry deleted.'));
    }

    public function export(Request $request)
    {
        $query = Auth::user()->losses()->with('product')->orderByDesc('loss_date');

        if ($request->filled('year')) {
            $query->whereYear('loss_date', $request->year);
        }

        $losses = $query->get();

        $csv = "Datum,Produkt,Kategorie,Menge,Einheit,Grund,Lieferant,EK-Preis,Gesamtwert,Notizen,Hash\n";
        foreach ($losses as $loss) {
            $csv .= implode(',', [
                $loss->loss_date->format('d.m.Y'),
                '"' . str_replace('"', '""', $loss->product->name) . '"',
                '"' . str_replace('"', '""', $loss->product->category ?? '') . '"',
                number_format($loss->quantity, 3, '.', ''),
                $loss->unit,
                Loss::reasonLabel($loss->reason),
                '"' . str_replace('"', '""', $loss->supplier ?? '') . '"',
                number_format($loss->purchase_price ?? 0, 2, '.', ''),
                number_format($loss->totalValue(), 2, '.', ''),
                '"' . str_replace('"', '""', $loss->notes ?? '') . '"',
                $loss->immutable_hash ?? '',
            ]) . "\n";
        }

        $year     = $request->filled('year') ? $request->year : date('Y');
        $filename = "schwund_export_{$year}.csv";

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
