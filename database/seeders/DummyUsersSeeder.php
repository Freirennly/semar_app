<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyUsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Anisa Pratiwi',
                'email' => 'student@semar.test',
                'nim_nip' => '20260042',
                'phone' => '081234567890',
                'password' => Hash::make('password'),
                'role' => 'student',
            ],
            [
                'name' => 'Rizky Fauzan',
                'email' => 'student2@semar.test',
                'nim_nip' => '20260051',
                'phone' => '081234567891',
                'password' => Hash::make('password'),
                'role' => 'student',
            ],
            [
                'name' => 'Dr. Rina Wijaya',
                'email' => 'reviewer@semar.test',
                'nim_nip' => '1980032001',
                'phone' => '081345678901',
                'password' => Hash::make('password'),
                'role' => 'reviewer',
            ],
            [
                'name' => 'Dr. Andi Prasetyo',
                'email' => 'reviewer2@semar.test',
                'nim_nip' => '1975061001',
                'phone' => '081345678902',
                'password' => Hash::make('password'),
                'role' => 'reviewer',
            ],
            [
                'name' => 'Prof. Haryono',
                'email' => 'ketua@semar.test',
                'nim_nip' => '1965051001',
                'phone' => '081456789012',
                'password' => Hash::make('password'),
                'role' => 'ketua',
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'sekretariat@semar.test',
                'nim_nip' => '1985071001',
                'phone' => '081567890123',
                'password' => Hash::make('password'),
                'role' => 'sekretariat',
            ],
            [
                'name' => 'Admin Sistem',
                'email' => 'admin@semar.test',
                'nim_nip' => 'ADM001',
                'phone' => '081678901234',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
            $user->syncRoles([$role]);
        }
    }
}
