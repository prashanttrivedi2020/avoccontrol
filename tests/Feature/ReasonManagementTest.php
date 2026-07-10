<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReasonManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_and_fetch_their_own_reasons(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'username' => 'reasonuser',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        $this->actingAs($user);

        $this->getJson(route('reasons.active'))
            ->assertOk()
            ->assertJsonFragment(['name' => 'Spoilage']);

        $this->postJson(route('reasons.store'), ['name' => 'Quality issue'])
            ->assertCreated()
            ->assertJsonPath('reason.name', 'Quality issue');

        $this->assertDatabaseHas('reasons', [
            'user_id' => $user->id,
            'name' => 'Quality issue',
        ]);
    }
}
