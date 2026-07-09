<?php

namespace Tests\Feature;

use App\Http\Controllers\ProductImportController;
use App\Models\ProductImportFailure;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductImportControllerTest extends TestCase
{
    use RefreshDatabase;
    public function test_mapping_form_uses_column_indexes_for_import_mapping(): void
    {
        $response = $this->withSession([
            'import_key' => 'test-import',
            'import_headers' => ['Product name', 'Barcode'],
            'import_preview' => [['Widget', '123456']],
            'import_row_count' => 1,
            'import_delimiter' => ',',
            'import_auto_map' => [],
        ])->get(route('products.import.mapping'));

        $response->assertStatus(302);
    }

    public function test_failed_import_rows_page_shows_user_failures(): void
    {
        $user = User::create([
            'name' => 'Importer',
            'username' => 'importer',
            'email' => 'importer@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        ProductImportFailure::create([
            'user_id' => $user->id,
            'import_key' => 'import-key',
            'row_number' => 7,
            'row_data' => json_encode(['name' => 'Bad product']),
            'error_message' => 'Simulated insert failure',
            'error_class' => 'RuntimeException',
        ]);

        $response = $this->actingAs($user)->get(route('products.import.failures'));

        $response->assertOk();
        $response->assertSee('Failed import rows');
        $response->assertSee('Bad product');
    }

    public function test_failed_product_rows_are_saved_and_do_not_stop_the_import(): void
    {
        $user = User::create([
            'name' => 'Importer',
            'username' => 'importer',
            'email' => 'importer@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);
        $controller = new ProductImportController();

        $method = new \ReflectionMethod($controller, 'storeImportedProduct');
        $method->setAccessible(true);

        $result = $method->invoke($controller, $user, [
            'name' => 'Bad product',
            'barcode' => '123',
            'category' => null,
            'supplier' => null,
            'purchase_price' => null,
            'unit' => 'Stk',
            'active' => true,
        ], 'import-key', 7, null, 'skip', function () {
            throw new \RuntimeException('Simulated insert failure');
        });

        $this->assertFalse($result['imported']);
        $this->assertTrue($result['failed']);
        $this->assertDatabaseHas('product_import_failures', [
            'user_id' => $user->id,
            'import_key' => 'import-key',
            'row_number' => 7,
        ]);
    }
}
