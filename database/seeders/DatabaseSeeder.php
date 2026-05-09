<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate([
            'email' => env('ADMIN_EMAIL', 'admin@bedsheets.ptree.lk'),
        ], [
            'name' => env('ADMIN_NAME', 'PMS Admin'),
            'password' => Hash::make(env('ADMIN_PASSWORD', 'ChangeMeNow!2026')),
        ]);

        $this->call(ProductCatalogSeeder::class);
    }
}
