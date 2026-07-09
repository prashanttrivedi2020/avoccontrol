<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImportFailure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImportController extends Controller
{
    /** Product fields the user can map CSV columns onto — translated at runtime */
    private static function fields(): array
    {
        return [
            'name'           => ['label' => __('Product name (required)'), 'required' => true],
            'barcode'        => ['label' => __('Barcode'),                 'required' => false],
            'category'       => ['label' => __('Category'),                'required' => false],
            'supplier'       => ['label' => __('Supplier'),                'required' => false],
            'purchase_price' => ['label' => __('Purchase price (€)'),      'required' => false],
            'unit'           => ['label' => __('Unit'),                    'required' => false],
        ];
    }

    // ── Step 1: show upload form ───────────────────────────────────────────
    public function showUpload()
    {
        return view('products.import-upload');
    }

    // ── Step 2: receive file, parse headers + preview, redirect to mapping ─
    public function upload(Request $request)
    {
        $serverMaxKb = min(5120, $this->parseIniSize(ini_get('upload_max_filesize')));
        $serverMaxMb = round($serverMaxKb / 1024, 1);

        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:' . $serverMaxKb],
        ], [
            'csv_file.required' => __('Please select a CSV file.'),
            'csv_file.mimes'    => __('File must be a .csv file.'),
            'csv_file.uploaded' => __('The CSV file failed to upload. Please try again.'),
            'csv_file.max'      => __('File may not be larger than :size MB.', ['size' => $serverMaxMb]),
        ]);

        $file = $request->file('csv_file');
        if (!$file || !$file->isValid()) {
            return back()->withErrors(['csv_file' => __('The CSV file failed to upload. Please try again.')]);
        }

        // Detect delimiter from first line
        $firstLine = fgets(fopen($file->getRealPath(), 'r'));
        $delimiter = $this->detectDelimiter($firstLine);

        // Parse headers + up to 5 preview rows
        $handle = fopen($file->getRealPath(), 'r');

        // Strip BOM if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        \Log::info("Detected delimiter: {$delimiter}");
        $headers = fgetcsv($handle, 0, $delimiter);
        if (!$headers || count($headers) < 1) {
            return back()->withErrors(['csv_file' => __('Could not read CSV file or no header row found.')]);
        }
        $headers = array_map('trim', $headers);

        $preview = [];
        $rowCount = 0;
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false && count($preview) < 5) {
            if (count(array_filter($row)) === 0) continue;
            $preview[] = array_map('trim', $row);
            $rowCount++;
        }
        // Count remaining rows
        while (fgetcsv($handle, 0, $delimiter) !== false) {
            $rowCount++;
        }
        fclose($handle);

        // Store the file temporarily
        $key  = Str::uuid()->toString();
        $path = 'imports/' . $key . '.csv';
        \Log::info( $file->getRealPath());
        Storage::disk('local')->put($path, file_get_contents($file->getRealPath()));

        // Auto-detect mapping: match header names to product fields
        $autoMap = [];
        $synonyms = [
            'name'           => ['name', 'produktname', 'produkt', 'bezeichnung', 'article', 'artikel', 'title', 'titel', 'product name', 'urun adi', 'ürün adı','Kurztext'],
            'barcode'        => ['barcode', 'ean', 'gtin', 'upc', 'code', 'artikelnummer', 'sku', 'barkod','FlagEAN'],
            'category'       => ['category', 'kategorie', 'warengruppe', 'gruppe', 'abteilung', 'kategori'],
            'supplier'       => ['supplier', 'lieferant', 'hersteller', 'vendor', 'brand', 'marke', 'tedarikci', 'tedarikçi'],
            'purchase_price' => ['purchase_price', 'ek-preis', 'ek preis', 'einkaufspreis', 'preis', 'price', 'cost', 'kosten', 'alis fiyati', 'alış fiyatı'],
            'unit'           => ['unit', 'einheit', 'uom', 'measure', 'maßeinheit', 'birim'],
        ];
        foreach ($headers as $i => $h) {
            $normalized = strtolower(trim($h));
            foreach ($synonyms as $field => $words) {
                if (in_array($normalized, $words, true)) {
                    \Log::info( $path).": Found '{$field}' for column '{$h}'";
                    $autoMap[$field] = $i;
                    break;
                }
            }
        }

        // Store state in session
        session([
            'import_key'       => $key,
            'import_headers'   => $headers,
            'import_preview'   => $preview,
            'import_row_count' => $rowCount,
            'import_delimiter' => $delimiter,
            'import_auto_map'  => $autoMap,
        ]);

        return redirect()->route('products.import.mapping');
    }

    private function parseIniSize(?string $value): int
    {
        if (!$value) {
            return 0;
        }

        $unit = strtolower(substr($value, -1));
        $size = (int) $value;

        return match ($unit) {
            'g' => $size * 1024 * 1024,
            'm' => $size * 1024,
            'k' => $size,
            default => $size,
        };
    }

    // ── Step 3: show column-mapping UI ─────────────────────────────────────
    public function showMapping()
    {
        if (!session('import_key')) {
            return redirect()->route('products.import.upload')
                ->withErrors(['csv_file' => __('Please upload a CSV file first.')]);
        }

        return view('products.import-mapping', [
            'fields'    => self::fields(),
            'headers'   => session('import_headers'),
            'preview'   => session('import_preview'),
            'rowCount'  => session('import_row_count'),
            'autoMap'   => session('import_auto_map', []),
        ]);
    }

    // ── Step 4: process the import ─────────────────────────────────────────
    public function failures()
    {
        $failures = Auth::user()
            ->productImportFailures()
            ->latest()
            ->get();

        return view('products.import-failures', [
            'failures' => $failures,
        ]);
    }

    public function process(Request $request)
    {
        if (!session('import_key')) {
            return redirect()->route('products.import.upload');
        }

        $mapping   = $request->input('map', []);
        $mapping   = array_map(static function ($value) {
            return is_numeric($value) ? (int) $value : null;
        }, $mapping);
        $skipFirst = $request->boolean('skip_header', true);
        $duplicate = $request->input('duplicate', 'skip');

        // Validate that 'name' is mapped
        if (!isset($mapping['name']) || $mapping['name'] === '') {
            return back()->withErrors(['mapping' => __("The field 'Product name' must be assigned to a column.")]);
        }

        $key       = session('import_key');
        $delimiter = session('import_delimiter', ';');
        $totalRows = session('import_row_count', 0);
        $realPath  = storage_path('app/private/imports/' . $key . '.csv');
        \Log::info($realPath);
        if (!file_exists($realPath)) {
            return redirect()->route('products.import.upload')
                ->withErrors(['csv_file' => __('The uploaded file has expired. Please upload again.')]);
        }

        $stream = function () use ($realPath, $delimiter, $totalRows, $mapping, $skipFirst, $duplicate, $key) {
            ignore_user_abort(true);
            set_time_limit(0);

            $handle = fopen($realPath, 'r');
            $bom = fread($handle, 3);
            if ($bom !== "ï»¿") {
                rewind($handle);
            }

            if ($skipFirst) {
                fgetcsv($handle, 0, $delimiter);
            }

            $imported  = 0;
            $skipped   = 0;
            $failed    = 0;
            $errors    = [];
            $user      = Auth::user();
            $rowNumber = 1;
            $processed = 0;
            $units     = ['Stk', 'kg', 'g', 'L', 'ml', 'Pkg', 'Bund', 'Kiste'];

            echo '<!doctype html><html><head><meta charset="UTF-8"><title>' . __('Importing products') . '</title></head><body style="font-family:system-ui, sans-serif;background:#0f2210;color:#edf3ea;padding:32px;">';
            echo '<div style="max-width:720px;margin:auto;padding:24px;background:#102713;border:1px solid rgba(255,255,255,0.07);border-radius:18px;">';
            echo '<h1 style="font-size:24px;margin-bottom:16px;">' . __('Importing products') . '</h1>';
            echo '<p id="progress-text" style="font-size:16px;color:#d4e7d4;">' . __('Starting import...') . '</p>';
            echo '<pre id="progress-log" style="margin-top:20px;padding:16px;background:#0d1f0d;border:1px solid rgba(255,255,255,0.05);border-radius:12px;line-height:1.5;color:#c7d5c7;white-space:pre-wrap;max-height:320px;overflow:auto;"></pre>';
            echo '</div>';
            echo '<script>function updateProgress(text){document.getElementById("progress-text").textContent=text;}function logLine(text){const el=document.getElementById("progress-log");el.textContent += text + "\\n";el.scrollTop = el.scrollHeight;}</script>';
            echo str_repeat(' ', 1024);
            if (ob_get_level()) {
                ob_flush();
            }
            flush();

            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $rowNumber++;
                $processed++;
                if (count(array_filter($row)) === 0) {
                     $processed++;
                    continue;
                }

                
                $progressLabel = __('Processing :current of :total...', ['current' => $processed, 'total' => $totalRows]);
                echo '<script>updateProgress(' . json_encode($progressLabel) . '); logLine(' . json_encode($progressLabel) . ');</script>';
                if (ob_get_level()) {
                    ob_flush();
                }
                flush();

                $get = function (string $field) use ($row, $mapping): ?string {
                    if (!isset($mapping[$field]) || $mapping[$field] === null || $mapping[$field] === '') {
                        return null;
                    }

                    $idx = (int) $mapping[$field];
                    return isset($row[$idx]) ? trim($row[$idx]) : null;
                };

                $name = $this->sanitizeImportValue($get('name'));
                if (!$name) {
                    $errors[] = "Row {$rowNumber}: product name missing — skipped.";
                    $skipped++;
                    $processed++;
                    continue;
                }

                $price = $get('purchase_price');
                if ($price !== null) {
                    $price = str_replace([' ', '.', ','], ['', '', '.'], preg_replace('/[^\d,.]/', '', $price));
                    $price = is_numeric($price) ? (float)$price : null;
                }

                $unit = $this->sanitizeImportValue($get('unit'));
                if (!$unit || !in_array($unit, $units, true)) {
                    $unit = 'Stk';
                }

                $data = [
                    'name'           => $name,
                    'barcode'        => $this->sanitizeImportValue($get('barcode')) ?: null,
                    'category'       => $this->sanitizeImportValue($get('category')) ?: null,
                    'supplier'       => $this->sanitizeImportValue($get('supplier')) ?: null,
                    'purchase_price' => $price,
                    'unit'           => $unit,
                    'active'         => true,
                ];

                $skip = blank($data['barcode']);
                if ($skip) {
                     $skipped++;
                      $processed++;
                    continue;
                }


                $existing = $user->products()->where('name', $name)->where('barcode', $data['barcode'])->first();
                $persistResult = $this->storeImportedProduct($user, $data, $key, $rowNumber, $existing, $duplicate);

                if ($persistResult['skipped']) {
                    $skipped++;
                     $processed++;
                    continue;
                }

                if ($persistResult['failed']) {
                    $failed++;
                     $processed++;
                    $errors[] = "Row {$rowNumber}: " . ($persistResult['error_message'] ?? __('failed to save product.'));
                    continue;
                }

                $imported++;
            }

            fclose($handle);
            if (file_exists($realPath)) {
                unlink($realPath);
            }

            session()->forget(['import_key', 'import_headers', 'import_preview', 'import_row_count', 'import_delimiter', 'import_auto_map']);

            $message = "{$imported} " . __('product(s) imported.');
            if ($skipped) $message .= " {$skipped} " . __('skipped.');
            if ($failed)  $message .= " {$failed} " . __('failed and saved for review.');
            if ($errors)  $message .= ' ' . count($errors) . ' ' . __('errors.');

            echo '<script>updateProgress(' . json_encode($message) . '); logLine(' . json_encode($message) . ');</script>';
            echo '<p style="margin-top:20px;color:#a9bca6;">' . __('You will be redirected in a moment...') . '</p>';
            echo '<p><a href="' . route('products.index') . '" style="color:#a8e388;">' . __('Return to products manually') . '</a></p>';
            echo '<script>setTimeout(function(){ window.location.href = ' . json_encode(route('products.index')) . '; }, 2500);</script>';
            echo '</body></html>';
        };

        return response()->stream($stream, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Cache-Control' => 'no-cache, must-revalidate',
            'X-Accel-Buffering' => 'no',
        ]);
    }
    // ── Helpers ────────────────────────────────────────────────────────────
    private function sanitizeImportValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        $value = str_replace(["\x00", "\x1a"], '', $value);
        $value = iconv('UTF-8', 'UTF-8//IGNORE', $value);

        return $value === false ? null : $value;
    }

    private function storeImportedProduct(User $user, array $data, string $importKey, int $rowNumber, ?Product $existing = null, string $duplicate = 'skip', ?callable $persistAction = null): array
    {
        try {
            if ($persistAction) {
                $persistAction($user, $data, $existing, $duplicate);
            } elseif ($existing) {
                if ($duplicate === 'overwrite') {
                    $existing->update($data);
                } else {
                    return ['imported' => false, 'failed' => false, 'skipped' => true];
                }
            } else {
                $user->products()->create($data);
            }

            return ['imported' => true, 'failed' => false, 'skipped' => false];
        } catch (\Throwable $e) {
            ProductImportFailure::create([
                'user_id' => $user->id,
                'import_key' => $importKey,
                'row_number' => $rowNumber,
                'row_data' => json_encode($data, JSON_UNESCAPED_UNICODE),
                'error_message' => $e->getMessage(),
                'error_class' => get_class($e),
            ]);

            return [
                'imported' => false,
                'failed' => true,
                'skipped' => false,
                'error_message' => $e->getMessage(),
            ];
        }
    }

    private function detectDelimiter(string $line): string
    {
        $counts = [
            ','  => substr_count($line, ','),
            ';'  => substr_count($line, ';'),
            "\t" => substr_count($line, "\t"),
            '|'  => substr_count($line, '|'),
        ];
        arsort($counts);
        return array_key_first($counts);
    }
}
