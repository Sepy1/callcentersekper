<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'it',
                'email' => 'it@a.com',
                'password' => Hash::make('secret'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now()
            ],
             [
                'name' => 'qa',
                'email' => 'qa@a.com',
                'password' => Hash::make('secret'),
                'role' => 'qa',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'admin',
                'email' => 'admin@a.com',
                'password' => Hash::make('secret'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Divisi Operasional',
                'email' => 'operasional@a.com',
                'password' => Hash::make('secret'),
                'role' => 'officer',
                'created_at' => now(),
                'updated_at' => now()
            ],
             [
                'name' => 'Divisi Penyelesaian Kredit',
                'email' => 'dpk@a.com',
                'password' => Hash::make('secret'),
                'role' => 'officer',
                'created_at' => now(),
                'updated_at' => now()
            ],
             [
                'name' => 'Divisi SDM Umum',
                'email' => 'sdmumum@a.com',
                'password' => Hash::make('secret'),
                'role' => 'officer',
                'created_at' => now(),
                'updated_at' => now()
            ],
             [
                'name' => 'Satuan Kerja Audit Internal',
                'email' => 'skai@a.com',
                'password' => Hash::make('secret'),
                'role' => 'officer',
                'created_at' => now(),
                'updated_at' => now()
            ],
             [
                'name' => 'Satuan Kerja Manajemen Risiko',
                'email' => 'skmr@a.com',
                'password' => Hash::make('secret'),
                'role' => 'officer',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Divisi Kepatuhan',
                'email' => 'kepatuhan@a.com',
                'password' => Hash::make('secret'),
                'role' => 'officer',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Divisi Pemasaran',
                'email' => 'pemasaran@a.com',
                'password' => Hash::make('secret'),
                'role' => 'officer',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
