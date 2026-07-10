<?php

namespace Tests\Feature;

use App\Models\Reason;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnitReasonManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_soft_delete_a_unit_from_the_management_page(): void
    {
        $user = User::create([
            'name' => 'Tester',
            'username' => 'tester',
            'email' => 'tester@example.com',
            'password' => 'secret123',
        ]);

        $unit = Unit::create([
            'user_id' => $user->id,
            'name' => 'Box',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->actingAs($user)->get('/units')
            ->assertStatus(200)
            ->assertSee('Box');

        $this->actingAs($user)->delete('/units/' . $unit->id)
            ->assertRedirect('/units');

        $this->assertSoftDeleted($unit);
    }

    public function test_user_can_soft_delete_a_reason_from_the_management_page(): void
    {
        $user = User::create([
            'name' => 'Tester',
            'username' => 'tester2',
            'email' => 'tester2@example.com',
            'password' => 'secret123',
        ]);

        $reason = Reason::create([
            'user_id' => $user->id,
            'name' => 'Damage',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->actingAs($user)->get('/reasons')
            ->assertStatus(200)
            ->assertSee('Damage');

        $this->actingAs($user)->delete('/reasons/' . $reason->id)
            ->assertRedirect('/reasons');

        $this->assertSoftDeleted($reason);
    }
}
