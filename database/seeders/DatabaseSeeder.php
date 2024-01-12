<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\District;
use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        Admin::truncate();
        Admin::insert([
            "login" => "998994630614",
            "password" => Hash::make("adminlead"),
            "role" => "admin"
        ]);
        
    }
}
