<?php

namespace Tests\Feature;

use App\Models\Loss;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_toggle_user_status_and_view_user_loss_report(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@avoccontrol.test',
            'role' => 'admin',
            'password' => Hash::make('secret123'),
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@avoccontrol.test',
            'role' => 'user',
            'is_active' => true,
        ]);

        $product = Product::create([
            'user_id' => $user->id,
            'name' => 'Milk Pack',
            'barcode' => '1234567890123',
            'category' => 'Dairy',
            'supplier' => 'Fresh Farm',
        ]);

        Loss::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'loss_date' => '2026-08-20',
            'quantity' => 2,
            'unit' => 'kg',
            'reason' => 'verderb',
            'supplier' => 'Fresh Farm',
            'purchase_price' => 6.50,
            'notes' => 'Temperature issue',
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/users');
        $response->assertOk();
        $response->assertSee($user->name);

        $toggle = $this->from('/admin/users')->post('/admin/users/' . $user->id . '/toggle-status');
        $toggle->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', ['id' => $user->id, 'is_active' => 0]);

        $report = $this->get('/admin/users/' . $user->id . '/losses');
        $report->assertOk();
        $report->assertSee(Loss::reasonLabel('verderb'));
        $report->assertSee($user->name);
    }
}
