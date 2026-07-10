<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductQuickCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_product_quickly_from_the_loss_form(): void
    {
        $user = User::create([
            'name' => 'Tester',
            'username' => 'tester',
            'email' => 'tester@example.com',
            'password' => 'secret123',
        ]);

        $response = $this->actingAs($user)->postJson('/api/products', [
            'name' => 'Quick Product',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('product.name', 'Quick Product')
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('products', [
            'user_id' => $user->id,
            'name' => 'Quick Product',
            'unit' => 'Stk',
        ]);
    }
}
