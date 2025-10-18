<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created_with_valid_data(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'role' => 'client',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'client',
        ]);
    }

    public function test_user_has_role(): void
    {
        $user = User::factory()->create(['role' => 'transporter']);

        $this->assertEquals('transporter', $user->role);
    }

    public function test_user_can_have_wastes(): void
    {
        $user = User::factory()->hasWastes(3)->create();

        $this->assertEquals(3, $user->wastes()->count());
    }

    public function test_user_can_have_orders_as_client(): void
    {
        $user = User::factory()->hasClientOrders(2)->create();

        $this->assertEquals(2, $user->clientOrders()->count());
    }

    public function test_user_can_have_orders_as_transporter(): void
    {
        $user = User::factory()->hasTransporterOrders(2)->create(['role' => 'transporter']);

        $this->assertEquals(2, $user->transporterOrders()->count());
    }

    public function test_user_email_is_unique(): void
    {
        User::factory()->create(['email' => 'unique@example.com']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        User::factory()->create(['email' => 'unique@example.com']);
    }
}
