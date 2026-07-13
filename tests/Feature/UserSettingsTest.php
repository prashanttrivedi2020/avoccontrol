<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_update_company_settings(): void
    {
        $user = User::factory()->create([
            'username' => 'tester',
            'name' => 'Tester User',
        ]);

        $this->actingAs($user);

        $response = $this->post(route('settings.update'), [
            'company_name' => 'Acme Ltd',
            'owner_name' => 'Jane Doe',
            'address' => '12 Main Street',
            'tax_number' => 'TR123456',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertSame('Acme Ltd', $user->company_name);
        $this->assertSame('Jane Doe', $user->owner_name);
        $this->assertSame('12 Main Street', $user->address);
        $this->assertSame('TR123456', $user->tax_number);
    }
}
