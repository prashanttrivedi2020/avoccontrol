<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Note: Units are now user-specific. Each user creates their own units.
     * This seeder is kept for backward compatibility but does nothing.
     */
    public function run(): void
    {
        // Units are created by each user individually
        // No default seeding needed since each user has their own units
    }
}
