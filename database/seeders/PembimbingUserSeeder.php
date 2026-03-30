<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class PembimbingUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'pembimbing@pins.test'],
            [
                'name' => 'Pembimbing PINS',
                'password' => Hash::make('password'),
                'role' => 'pembimbing',
            ]
        );
    }
}
