<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\Waste;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WasteTest extends TestCase
{
    use RefreshDatabase;

    public function test_waste_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $waste = Waste::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $waste->user);
        $this->assertEquals($user->id, $waste->user->id);
    }

    public function test_waste_can_be_created_with_valid_data(): void
    {
        $user = User::factory()->create();
        
        $waste = Waste::create([
            'user_id' => $user->id,
            'type' => 'Plastic',
            'weight' => 5.5,
            'image' => 'waste.jpg',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('wastes', [
            'user_id' => $user->id,
            'type' => 'Plastic',
            'weight' => 5.5,
        ]);
    }

    public function test_waste_status_can_be_updated(): void
    {
        $waste = Waste::factory()->create(['status' => 'pending']);

        $waste->update(['status' => 'approved']);

        $this->assertEquals('approved', $waste->fresh()->status);
    }

    public function test_user_can_have_multiple_wastes(): void
    {
        $user = User::factory()->create();
        $wastes = Waste::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertEquals(3, $user->wastes()->count());
    }
}
