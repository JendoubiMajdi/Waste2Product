<?php

namespace Database\Seeders;

use App\Models\CollectionPoint;
use App\Models\User;
use Illuminate\Database\Seeder;

class CollectionPointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first collector user, or create one if none exists
        $collector = User::where('role', 'collector')->first();

        if (! $collector) {
            $collector = User::create([
                'name' => 'System Collector',
                'email' => 'collector@waste2product.com',
                'password' => bcrypt('password'),
                'role' => 'collector',
            ]);
        }

        $collectionPoints = [
            [
                'name' => 'Casablanca Central Recycling Center',
                'address' => 'Boulevard Mohammed V, Casablanca',
                'latitude' => 33.5731,
                'longitude' => -7.5898,
                'working_hours' => '08:00-18:00',
                'contact_phone' => '+212 522-123456',
                'status' => 'active',
                'user_id' => $collector->id,
            ],
            [
                'name' => 'Maarif Collection Point',
                'address' => 'Rue Abou Hanifa Al Nooman, Maarif, Casablanca',
                'latitude' => 33.5892,
                'longitude' => -7.6308,
                'working_hours' => '09:00-17:00',
                'contact_phone' => '+212 522-234567',
                'status' => 'active',
                'user_id' => $collector->id,
            ],
            [
                'name' => 'Ain Diab Eco Center',
                'address' => 'Boulevard de la Corniche, Ain Diab, Casablanca',
                'latitude' => 33.5963,
                'longitude' => -7.6678,
                'working_hours' => '08:30-19:00',
                'contact_phone' => '+212 522-345678',
                'status' => 'active',
                'user_id' => $collector->id,
            ],
            [
                'name' => 'Anfa Waste Collection',
                'address' => 'Boulevard d\'Anfa, Casablanca',
                'latitude' => 33.5837,
                'longitude' => -7.6329,
                'working_hours' => '07:00-16:00',
                'contact_phone' => '+212 522-456789',
                'status' => 'active',
                'user_id' => $collector->id,
            ],
            [
                'name' => 'Sidi Maarouf Green Point',
                'address' => 'Zone Industrielle Sidi Maarouf, Casablanca',
                'latitude' => 33.5148,
                'longitude' => -7.6574,
                'working_hours' => '08:00-20:00',
                'contact_phone' => '+212 522-567890',
                'status' => 'active',
                'user_id' => $collector->id,
            ],
            [
                'name' => 'Hay Hassani Collection Center',
                'address' => 'Avenue des FAR, Hay Hassani, Casablanca',
                'latitude' => 33.5586,
                'longitude' => -7.6428,
                'working_hours' => '09:00-18:00',
                'contact_phone' => '+212 522-678901',
                'status' => 'active',
                'user_id' => $collector->id,
            ],
            [
                'name' => 'Bourgogne Recycling Hub',
                'address' => 'Boulevard Bourgogne, Casablanca',
                'latitude' => 33.5823,
                'longitude' => -7.6172,
                'working_hours' => '08:00-17:30',
                'contact_phone' => '+212 522-789012',
                'status' => 'active',
                'user_id' => $collector->id,
            ],
            [
                'name' => 'California Eco Station',
                'address' => 'Quartier California, Casablanca',
                'latitude' => 33.5461,
                'longitude' => -7.6714,
                'working_hours' => '10:00-19:00',
                'contact_phone' => '+212 522-890123',
                'status' => 'active',
                'user_id' => $collector->id,
            ],
        ];

        foreach ($collectionPoints as $point) {
            CollectionPoint::create($point);
        }

        $this->command->info('Collection points seeded successfully!');
    }
}
