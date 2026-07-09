from pathlib import Path

path = Path('app/Http/Controllers/ProductImportController.php')
text = path.read_text(encoding='utf-8')
start_marker = '    // ── Step 4: process the import ─────────────────────────────────────────'
end_marker = '    // ── Helpers'
start = text.index(start_marker)
end = text.index(end_marker, start)
new = '''    // ── Step 4: process the import ─────────────────────────────────────────
    public function process(Request $request)
    {
        if (!session('import_key')) {
            return redirect()->route('products.import.upload');
        }

        $mapping   = $request->input('map', []);
        $skipFirst = $request->boolean('skip_header', true);
        $duplicate = $request->input('duplicate', 'skip');

        // Validate that 'name' is mapped
        if (!isset($mapping['name']) || $mapping['name'] === '') {
            return back()->withErrors(['mapping' => __("The field 'Product name' must be assigned to a column.")]);
        }

        $key       = session('import_key');
        $delimiter = session('import_delimiter', ',');
        $totalRows = session('import_row_count', 0);
        $realPath  = storage_path('app/imports/' . $key . '.csv');

        if (!file_exists($realPath)) {
            return redirect()->route('products.import.upload')
                ->withErrors(['csv_file' => __('The uploaded file has expired. Please upload again.')]);
        }

        $stream = function () use ($realPath, $delimiter, $totalRows, $mapping, $skipFirst, $duplicate) {
            ignore_user_abort(true);
            set_time_limit(0);

            $handle = fopen($realPath, 'r');
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }

            if ($skipFirst) {
                fgetcsv($handle, 0, $delimiter);
            }

            $imported  = 0;
            $skipped   = 0;
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
            echo '<script>function updateProgress(text){document.getElementById("progress-text").textContent=text;}function logLine(text){const el=document.getElementById("progress-log");el.textContent += text + "\n";el.scrollTop = el.scrollHeight;}</script>';
            echo str_repeat(' ', 1024);
            if (ob_get_level()) {
                ob_flush();
            }
            flush();

            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $rowNumber++;
                if (count(array_filter($row)) === 0) {
                    continue;
                }

                $processed++;
                $progressLabel = __('Processing :current of :total...', ['current' => $processed, 'total' => $totalRows]);
                echo '<script>updateProgress(' . json_encode($progressLabel) . '); logLine(' . json_encode($progressLabel) . ');</script>';
                if (ob_get_level()) {
                    ob_flush();
                }
                flush();

                $get = function (string $field) use ($row, $mapping): ?string {
                    if (!isset($mapping[$field]) || $mapping[$field] === '') return null;
                    $idx = (int) $mapping[$field];
                    return isset($row[$idx]) ? trim($row[$idx]) : null;
                };

                $name = $get('name');
                if (!$name) {
                    $errors[] = "Row {$rowNumber}: product name missing — skipped.";
                    $skipped++;
                    continue;
                }

                $price = $get('purchase_price');
                if ($price !== null) {
                    $price = str_replace([' ', '.', ','], ['', '', '.'], preg_replace('/[^\d,.]/', '', $price));
                    $price = is_numeric($price) ? (float)$price : null;
                }

                $unit = $get('unit');
                if (!$unit || !in_array($unit, $units, true)) {
                    $unit = 'Stk';
                }

                $data = [
                    'name'           => $name,
                    'barcode'        => $get('barcode') ?: null,
                    'category'       => $get('category') ?: null,
                    'supplier'       => $get('supplier') ?: null,
                    'purchase_price' => $price,
                    'unit'           => $unit,
                    'active'         => true,
                ];

                $existing = $user->products()->where('name', $name)->first();

                if ($existing) {
                    if ($duplicate === 'overwrite') {
                        $existing->update($data);
                        $imported++;
                    } else {
                        $skipped++;
                    }
                    continue;
                }

                $user->products()->create($data);
                $imported++;
            }

            fclose($handle);
            if (file_exists($realPath)) {
                unlink($realPath);
            }

            session()->forget(['import_key', 'import_headers', 'import_preview', 'import_row_count', 'import_delimiter', 'import_auto_map']);

            $message = "{$imported} " . __('product(s) imported.');
            if ($skipped) $message .= " {$skipped} " . __('skipped.');
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
'''
path.write_text(text[:start] + new + text[end:], encoding='utf-8')
print('patched')
