<?php

namespace App\Http\Controllers;

use App\Models\Loss;
use App\Models\Product;
use App\Models\Reason;
use App\Models\Unit;
use Dompdf\Dompdf;
use Dompdf\Options;
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
       // $products = Auth::user()->products()->orderBy('name')->get();

        //return view('losses.index', compact('losses', 'products'));
        return view('losses.index', compact('losses'));
    }

    public function create()
    {
        $products = Auth::user()->products()->where('active', true)->orderBy('name')->get();
        $units = Auth::user()->units()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $reasons = Reason::getAllowedNamesForUser(Auth::user());
        return view('losses.create', compact('products', 'units', 'reasons'));
    }

    public function store(Request $request)
    {
        \Log::info(json_encode($request->all()));
        $allowedReasons = Reason::getAllowedNamesForUser(Auth::user(), $request->input('reason'));
        $data = $request->validate([
            'product_id'     => ['required', 'exists:products,id'],
            'loss_date'      => ['required', 'date', 'before_or_equal:today'],
            'quantity'       => ['required', 'numeric', 'min:0.001'],
            'unit'           => ['required', 'string', 'max:20'],
            'reason'         => ['required', 'string', 'max:50', 'in:' . implode(',', array_map('trim', $allowedReasons))],
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

        //return redirect()->route('losses.index')->with('success', __('Loss successfully documented.'));
         return redirect()->route('dashboard')->with('success', __('Loss successfully documented.'));
    }

    public function show(Loss $loss)
    {
        $this->authorize('view', $loss);
        return view('losses.show', compact('loss'));
    }

    public function edit(Loss $loss)
    {
        $this->authorize('update', $loss);

       // $products = Auth::user()->products()->where('active', true)->orderBy('name')->get();
        $units = Auth::user()->units()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $reasons = Reason::getAllowedNamesForUser(Auth::user());

        //return view('losses.edit', compact('loss', 'products', 'units'));
        return view('losses.edit', compact('loss', 'units', 'reasons'));
    }

    public function update(Request $request, Loss $loss)
    {
        $this->authorize('update', $loss);

        $allowedReasons = Reason::getAllowedNamesForUser(Auth::user(), $request->input('reason'));
        $data = $request->validate([
            // 'product_id'     => ['required', 'exists:products,id'],
            'product_name'   => ['nullable', 'string', 'max:255'],
            'loss_date'      => ['required', 'date', 'before_or_equal:today'],
            'quantity'       => ['required', 'numeric', 'min:0.001'],
            'unit'           => ['required', 'string', 'max:20'],
            'reason'         => ['required', 'string', 'max:50', 'in:' . implode(',', array_map('trim', $allowedReasons))],
            'supplier'       => ['nullable', 'string', 'max:255'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'photo'          => ['nullable', 'image', 'max:10240'],
            'photo_data'     => ['nullable', 'string'],
            'remove_photo'   => ['nullable', 'boolean'],
            'notes'          => ['nullable', 'string', 'max:1000'],
        ], [
            // 'product_id.required' => __('Please select a product.'),
            'loss_date.required'  => __('Date is required.'),
            'quantity.required'   => __('Quantity is required.'),
            'reason.required'     => __('Reason is required.'),
        ]);

        //$product = Auth::user()->products()->findOrFail($data['product_id']);

        if ($request->filled('photo_data')) {
            $dataUrl = $request->input('photo_data');
            if (preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $m)) {
                $ext       = $m[1] === 'png' ? 'png' : 'jpg';
                $imageData = base64_decode(substr($dataUrl, strpos($dataUrl, ',') + 1));
                $filename  = 'loss-photos/' . Str::uuid() . '.' . $ext;
                if ($loss->photo_path) {
                    Storage::disk('public')->delete($loss->photo_path);
                }
                Storage::disk('public')->put($filename, $imageData);
                $data['photo_path'] = $filename;
            }
        } elseif ($request->boolean('remove_photo')) {
            if ($loss->photo_path) {
                Storage::disk('public')->delete($loss->photo_path);
            }
            $data['photo_path'] = null;
        } elseif ($request->hasFile('photo')) {
            if ($loss->photo_path) {
                Storage::disk('public')->delete($loss->photo_path);
            }
            $path = $request->file('photo')->store('loss-photos', 'public');
            $data['photo_path'] = $path;
        } else {
            unset($data['photo']);
        }

        unset($data['photo_data']);

        // if (!empty($request->input('product_name'))) {
        //     $product->update(['name' => $request->input('product_name')]);
        // }

        if (empty($data['supplier'])) {
            $data['supplier'] = $data['supplier'];
        }
        if (empty($data['purchase_price'])) {
            $data['purchase_price'] = $data['purchase_price'];
        }

        $loss->update($data);

        return redirect()->route('losses.index')->with('success', __('Entry updated.'));
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

        $html = <<<'HTML'
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #222; }
        h1 { margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #d0d7de; padding: 6px; vertical-align: top; }
        th { background-color: #f6f8fa; text-align: left; }
        .photo-cell { width: 100px; height: 100px; text-align: center; }
        img { max-width: 100px; max-height: 100px; border: 1px solid #e5e7eb; border-radius: 4px; }
        .muted { color: #57606a; }
    </style>
</head>
<body>
HTML;

        $html .= '<h1>Schwund-Export</h1>';
        $html .= '<p>Jahr: ' . e($request->filled('year') ? $request->year : date('Y')) . '</p>';

        $html .= '<table>';
        $html .= '<thead><tr><th>Datum</th><th>Produkt</th><th>Kategorie</th><th>Menge</th><th>Grund</th><th>Lieferant</th><th>EK-Preis</th><th>Gesamtwert</th><th>Notizen</th><th class="photo-cell">Foto</th></tr></thead>';
        $html .= '<tbody>';

        foreach ($losses as $loss) {
            $photoHtml = '<span class="muted">Kein Foto</span>';
            if (! empty($loss->photo_path) && Storage::disk('public')->exists($loss->photo_path)) {
                $imagePath = Storage::disk('public')->path($loss->photo_path);
                $mimeType = mime_content_type($imagePath) ?: 'image/jpeg';
                $imageData = base64_encode(file_get_contents($imagePath));
                $photoHtml = '<img width="100" height="100" style="width: 100px; height: 100px;" src="data:' . $mimeType . ';base64,' . $imageData . '" alt="Loss photo">';
            }

            $html .= '<tr>';
            $html .= '<td>' . e($loss->loss_date?->format('d.m.Y') ?? '') . '</td>';
            $html .= '<td>' . e($loss->product->name ?? '') . '</td>';
            $html .= '<td>' . e($loss->product->category ?? '') . '</td>';
            $html .= '<td>' . e(number_format($loss->quantity, 3, '.', '')) . ' ' . e($loss->unit) . '</td>';
            $html .= '<td>' . e(Loss::reasonLabel($loss->reason)) . '</td>';
            $html .= '<td>' . e($loss->supplier ?? '') . '</td>';
            $html .= '<td>' . e(number_format($loss->purchase_price ?? 0, 2, '.', '')) . '</td>';
            $html .= '<td>' . e(number_format($loss->totalValue(), 2, '.', '')) . '</td>';
            $html .= '<td>' . e($loss->notes ?? '') . '</td>';
            $html .= '<td class="photo-cell">' . $photoHtml . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        $html .= '</body></html>';

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $pdf = new Dompdf($options);
        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        $year = $request->filled('year') ? $request->year : date('Y');
        $filename = "schwund_export_{$year}.pdf";

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
