<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the essential content required for the website to work.
     *
     * Demonstration content is seeded separately with: php artisan db:seed --class=DemoSeeder
     * Administrator accounts are never seeded; create one with: php artisan tindziva:create-admin
     */
    public function run(): void
    {
        $this->call([
            SettingsSeeder::class,
            PageSeeder::class,
            ServiceSeeder::class,
            ClientSeeder::class,
        ]);
    }
}
