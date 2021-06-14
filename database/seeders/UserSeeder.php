<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::query()->create([
        'email'=>'user1@test.com',
        'password'=>Hash::make(12345678),
        'name' => 'user1',
        'role_id' => 1,
    ]);
        User::query()->create([
            'email'=>'user2@test.com',
            'password'=>Hash::make(12345678),
            'name' => 'user2',
            'role_id' => 1,
        ]);
        User::query()->create([
            'email'=>'user3@test.com',
            'password'=>Hash::make(12345678),
            'name' => 'user3',
            'role_id' => 1,
        ]);
        User::query()->create([
            'email'=>'user4@test.com',
            'password'=>Hash::make(12345678),
            'name' => 'user4',
            'role_id' => 1,
        ]);
        User::query()->create([
            'email'=>'user5@test.com',
            'password'=>Hash::make(12345678),
            'name' => 'user5',
            'role_id' => 1,
        ]);
        User::query()->create([
            'email'=>'user6@test.com',
            'password'=>Hash::make(12345678),
            'name' => 'user6',
            'role_id' => 1,
        ]);
    }
}
