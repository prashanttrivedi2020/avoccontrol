<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LossesExportRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_loss_export_route(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'username' => 'testuser',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('losses.export', ['year' => now()->year]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }
}
