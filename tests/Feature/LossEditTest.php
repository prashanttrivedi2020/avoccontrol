<?php

namespace Tests\Feature;

use App\Models\Loss;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LossEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_edit_and_update_a_loss_entry(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'username' => 'testuser',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        $product = Product::create([
            'user_id' => $user->id,
            'name' => 'Milk',
            'barcode' => '123456',
            'category' => 'Dairy',
            'supplier' => 'Supplier A',
            'purchase_price' => 2.50,
            'unit' => 'Stk',
            'active' => true,
        ]);

        $loss = Loss::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'loss_date' => now()->toDateString(),
            'quantity' => 3,
            'unit' => 'Stk',
            'reason' => 'verderb',
            'supplier' => 'Supplier A',
            'purchase_price' => 2.50,
            'notes' => 'old note',
        ]);

        $this->actingAs($user);

        $this->get(route('losses.edit', $loss))
            ->assertOk();

        $this->put(route('losses.update', $loss), [
            'product_id' => $product->id,
            'loss_date' => now()->toDateString(),
            'quantity' => 4,
            'unit' => 'Stk',
            'reason' => 'diebstahl',
            'supplier' => 'Supplier B',
            'purchase_price' => 3.00,
            'notes' => 'updated note',
        ])
            ->assertRedirect(route('losses.index'));

        $this->assertDatabaseHas('losses', [
            'id' => $loss->id,
            'reason' => 'diebstahl',
            'notes' => 'updated note',
            'supplier' => 'Supplier B',
        ]);
    }

    public function test_authenticated_user_can_update_a_loss_entry_with_captured_photo_data(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'username' => 'testuser2',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        $product = Product::create([
            'user_id' => $user->id,
            'name' => 'Milk',
            'barcode' => '654321',
            'category' => 'Dairy',
            'supplier' => 'Supplier A',
            'purchase_price' => 2.50,
            'unit' => 'Stk',
            'active' => true,
        ]);

        $loss = Loss::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'loss_date' => now()->toDateString(),
            'quantity' => 3,
            'unit' => 'Stk',
            'reason' => 'verderb',
            'supplier' => 'Supplier A',
            'purchase_price' => 2.50,
            'notes' => 'old note',
        ]);

        $this->actingAs($user);

        $photoData = 'data:image/jpeg;base64,' . base64_encode('fake-camera-photo');

        $this->put(route('losses.update', $loss), [
            'loss_date' => now()->toDateString(),
            'quantity' => 4,
            'unit' => 'Stk',
            'reason' => 'diebstahl',
            'supplier' => 'Supplier B',
            'purchase_price' => 3.00,
            'notes' => 'updated note',
            'photo_data' => $photoData,
        ])
            ->assertRedirect(route('losses.index'));

        $loss->refresh();

        $this->assertNotNull($loss->photo_path);
        $this->assertTrue(Storage::disk('public')->exists($loss->photo_path));
    }
}
