<?php

namespace Database\Seeders;

use App\Http\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role_mode = new Role();
        DB::table('roles')->insert([
            [
                'id' => 1,
                'uuid' => Str::uuid(),
                'name' => 'admin',
                'display_name' => 'Administrator',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'uuid' => Str::uuid(),
                'name' => 'member',
                'display_name' => 'Member',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);        
    }
}
