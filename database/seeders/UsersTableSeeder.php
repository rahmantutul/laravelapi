<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $usersData=[
            ['name'=>'Admin','email'=>'admin@admin.com','password'=>bcrypt('password')],
            ['name'=>'User','email'=>'user@user.com','password'=>bcrypt('password')],
        ];

        User::insert($usersData);
    }
}
