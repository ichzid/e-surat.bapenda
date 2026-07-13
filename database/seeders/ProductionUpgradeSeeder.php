<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class ProductionUpgradeSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('departments')) {
            return;
        }

        $now = now();
        $departmentNames = [
            'SEKRETARIAT',
            'BIDANG PERENCANAAN DAN PENGEMBANGAN',
            'BIDANG PELAYANAN DAN PENETAPAN',
            'BIDANG PENDATAAN DAN PENILAIAN',
            'BIDANG PENAGIHAN KEBERATAN DAN PELAPORAN',
        ];

        foreach ($departmentNames as $name) {
            DB::table('departments')->updateOrInsert(
                ['name' => $name],
                ['updated_at' => $now, 'created_at' => $now]
            );
        }

        if (Schema::hasTable('users')) {
            DB::table('users')->where('role', 'admin')->update(['role' => 'administrator']);
            DB::table('users')->where('role', 'user')->update(['role' => 'operator']);

            if (Schema::hasColumn('users', 'department_id')) {
                $departmentIds = DB::table('departments')->pluck('id', 'name');
                $sekretariatId = $departmentIds['SEKRETARIAT'] ?? null;

                if ($sekretariatId) {
                    DB::table('users')
                        ->where('role', 'operator')
                        ->whereNull('department_id')
                        ->update(['department_id' => $sekretariatId]);
                }

                $users = [
                    'admin' => [
                        'name' => 'Administrator',
                        'role' => 'administrator',
                        'department_id' => null,
                    ],
                    'surat' => [
                        'name' => 'Operator Sekretariat',
                        'role' => 'operator',
                        'department_id' => $sekretariatId,
                    ],
                    'op_perencanaan' => [
                        'name' => 'Operator Perencanaan & Pengembangan',
                        'role' => 'operator',
                        'department_id' => $departmentIds['BIDANG PERENCANAAN DAN PENGEMBANGAN'] ?? null,
                    ],
                    'op_pelayanan' => [
                        'name' => 'Operator Pelayanan & Penetapan',
                        'role' => 'operator',
                        'department_id' => $departmentIds['BIDANG PELAYANAN DAN PENETAPAN'] ?? null,
                    ],
                    'op_pendataan' => [
                        'name' => 'Operator Pendataan & Penilaian',
                        'role' => 'operator',
                        'department_id' => $departmentIds['BIDANG PENDATAAN DAN PENILAIAN'] ?? null,
                    ],
                    'op_penagihan' => [
                        'name' => 'Operator Penagihan, Keberatan & Pelaporan',
                        'role' => 'operator',
                        'department_id' => $departmentIds['BIDANG PENAGIHAN KEBERATAN DAN PELAPORAN'] ?? null,
                    ],
                    'sekretaris' => [
                        'name' => 'Sekretaris',
                        'role' => 'sekretaris',
                        'department_id' => $sekretariatId,
                    ],
                    'kaban' => [
                        'name' => 'Kepala Badan',
                        'role' => 'kepala_badan',
                        'department_id' => null,
                    ],
                ];

                foreach ($users as $username => $user) {
                    $exists = DB::table('users')->where('username', $username)->exists();

                    DB::table('users')->updateOrInsert(
                        ['username' => $username],
                        array_merge($user, [
                            'password' => $exists ? DB::raw('password') : Hash::make('123456'),
                            'is_active' => true,
                            'updated_at' => $now,
                            'created_at' => $now,
                        ])
                    );
                }
            }
        }

        if (Schema::hasTable('documents')) {
            if (Schema::hasColumn('documents', 'status')) {
                DB::table('documents')
                    ->where(function ($query) {
                        $query->whereNull('status')->orWhere('status', 'draft');
                    })
                    ->update(['status' => 'selesai']);
            }

            if (Schema::hasColumn('documents', 'department_id')) {
                $sekretariatId = DB::table('departments')->where('name', 'SEKRETARIAT')->value('id');

                if ($sekretariatId) {
                    DB::table('documents')
                        ->whereNull('department_id')
                        ->update(['department_id' => $sekretariatId]);
                }
            }
        }
    }
}
