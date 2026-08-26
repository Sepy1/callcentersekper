<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $defaultPassword = env('SEEDER_USER_PASSWORD', 'secret');

        $users = [
            ['name' => 'it', 'email' => 'it@bkkjateng.co.id', 'role' => 'admin'],
            ['name' => 'admin', 'email' => 'admin@bkkjateng.co.id', 'role' => 'admin'],
            ['name' => 'qa', 'email' => 'qa@bkkjateng.co.id', 'role' => 'qa'],
            ['name' => 'Divisi Operasional', 'email' => 'operasional@bkkjateng.co.id', 'role' => 'officer', 'no_hp' => '6285725681860'],
            ['name' => 'Divisi Penyelesaian Kredit', 'email' => 'dpk@bkkjateng.co.id', 'role' => 'officer', 'no_hp' => '6285725681860'],
            ['name' => 'Divisi SDM Umum', 'email' => 'sdmumum@bkkjateng.co.id', 'role' => 'officer', 'no_hp' => '6285725681860'],
            ['name' => 'Satuan Kerja Audit Internal', 'email' => 'skai@bkkjateng.co.id', 'role' => 'officer', 'no_hp' => '6285725681860'],
            ['name' => 'Satuan Kerja Manajemen Risiko', 'email' => 'skmr@bkkjateng.co.id', 'role' => 'officer', 'no_hp' => '6285725681860'],
            ['name' => 'Divisi Kepatuhan', 'email' => 'kepatuhan@bkkjateng.co.id', 'role' => 'officer', 'no_hp' => '6285725681860'],
            ['name' => 'Divisi Pemasaran', 'email' => 'pemasaran@bkkjateng.co.id', 'role' => 'officer', 'no_hp' => '6285725681860'],
            [
                'name' => 'KCU',
                'email' => 'cabang001@bkkjateng.co.id',
                'role' => 'cabang',
                'kode_kantor' => '001',
            ],
             [
                'name' => 'Pusat',
                'email' => 'kcu@bkkjateng.co.id',
                'role' => 'cabang',
                'kode_kantor' => '000',
            ],
        ];

        foreach ($users as $attributes) {
            $user = User::firstOrNew(['email' => $attributes['email']]);
            $user->fill($attributes);

            if (! $user->exists) {
                $user->password = Hash::make($defaultPassword);
            }

            $user->save();
        }
    }
}
