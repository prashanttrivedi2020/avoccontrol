<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_search_their_products_by_name_from_the_loss_form(): void
    {
        $user = User::create([
            'name' => 'Tester',
            'username' => 'tester',
            'email' => 'tester@example.com',
            'password' => 'secret123',
        ]);

        $matchingProduct = Product::create([
            'user_id' => $user->id,
            'name' => 'Milk 1L',
            'barcode' => '1234567890123',
            'unit' => 'Stk',
            'active' => true,
        ]);

        Product::create([
            'user_id' => $user->id,
            'name' => 'Bread',
            'unit' => 'Stk',
            'active' => true,
        ]);

        $response = $this->actingAs($user)->getJson('/api/products/search?query=milk');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'products')
            ->assertJsonPath('products.0.id', $matchingProduct->id)
            ->assertJsonPath('products.0.name', 'Milk 1L');
    }
}
