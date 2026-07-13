<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
                $sekretariatId = DB::table('departments')->where('name', 'SEKRETARIAT')->value('id');

                if ($sekretariatId) {
                    DB::table('users')
                        ->where('role', 'operator')
                        ->whereNull('department_id')
                        ->update(['department_id' => $sekretariatId]);
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
