<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            DB::beginTransaction();
            Admin::truncate();
            Admin::create([
                'name' => 'Super Admin',
                'email' => 'superadmin@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => 1, // 1 for admin
            ]);
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Admin seeder error: ' . $e->getMessage());
        }
    }
}
