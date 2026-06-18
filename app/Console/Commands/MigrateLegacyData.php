<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Document;
use Illuminate\Support\Str;

#[Signature('migrate:legacy-data {file? : The path to the legacy sql file}')]
#[Description('Migrate legacy esurat CI3 database to Laravel 13 structure')]
class MigrateLegacyData extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');
        
        if (!$filePath) {
            $filePath = base_path('aplikasi_esurat.sql');
        }

        if (!file_exists($filePath)) {
            $this->error("File SQL tidak ditemukan di: {$filePath}");
            return 1;
        }

        $this->info("Memulai migrasi dari file: {$filePath}");
        
        try {
            // Karena DDL (CREATE/DROP TABLE) sering kali menyebabkan commit implisit 
            // pada MySQL, kita jalankan setup skema di luar blok transaksi data.
            $this->info("Menyiapkan skema temporary...");
            $this->setupTemporarySchema();
            
            $this->info("Memuat data SQL ke skema temporary...");
            $sqlContent = file_get_contents($filePath);
            DB::unprepared($sqlContent);

            // Mulai transaksi khusus untuk DML (Insert data)
            DB::beginTransaction();

            // 3. Pindahkan data Users
            $this->info("Memigrasi tabel users...");
            $this->migrateUsers();

            // 4. Pindahkan data Arsip/Documents
            $this->info("Memigrasi tabel documents...");
            $this->migrateDocuments();

            DB::commit();

            // 5. Bersihkan skema temporary (DDL, di luar transaksi)
            $this->info("Membersihkan skema temporary...");
            $this->cleanupTemporarySchema();

            $this->info("Migrasi data legacy berhasil diselesaikan!");
            return 0;

        } catch (\Exception $e) {
            // Coba rollback jika transaksi masih aktif
            try {
                if (DB::transactionLevel() > 0) {
                    DB::rollBack();
                }
            } catch (\Exception $rollbackEx) {
                // Ignore rollback error
            }
            
            $this->error("Terjadi kesalahan saat migrasi: " . $e->getMessage());
            return 1;
        }
    }

    private function setupTemporarySchema()
    {
        // Drop jika sudah ada
        $this->cleanupTemporarySchema();
        
        // Buat ulang tabel temporary sesuai struktur CI3 lama
        DB::statement('CREATE TABLE temp_user (id_user INT PRIMARY KEY AUTO_INCREMENT, username VARCHAR(255), password VARCHAR(255), nama_lengkap VARCHAR(255), id_level_user INT, id_bidang INT)');
        DB::statement('CREATE TABLE temp_arsip (id_arsip INT PRIMARY KEY AUTO_INCREMENT, no_arsip VARCHAR(255), no_surat VARCHAR(255), tanggal_surat DATE, tanggal_upload DATE, perihal TEXT, pengirim_surat VARCHAR(255), penerima_surat VARCHAR(255), file_surat VARCHAR(255), id_bidang INT, id_user INT, id_kategori INT)');
    }

    private function cleanupTemporarySchema()
    {
        DB::statement('DROP TABLE IF EXISTS temp_user');
        DB::statement('DROP TABLE IF EXISTS temp_arsip');
        // Drop tabel asli dari CI3 (user, arsip, kategori, level_user) jika ikut ter-import oleh file .sql
        DB::statement('DROP TABLE IF EXISTS user');
        DB::statement('DROP TABLE IF EXISTS arsip');
        DB::statement('DROP TABLE IF EXISTS kategori');
        DB::statement('DROP TABLE IF EXISTS level_user');
    }

    private function migrateUsers()
    {
        // Ambil data langsung dari tabel bawaan CI3
        $legacyUsers = DB::table('user')->get();
        
        foreach ($legacyUsers as $user) {
            $role = 'user';
            if ($user->id_level_user == 1) {
                $role = 'admin';
            }
            
            DB::table('users')->updateOrInsert(
                ['username' => $user->username],
                [
                    'name' => $user->nama_lengkap ?? 'Unknown',
                    'password' => $user->password,
                    'role' => $role,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    private function migrateDocuments()
    {
        // Ambil data langsung dari tabel arsip bawaan CI3
        $legacyDocuments = DB::table('arsip')->get();
        
        foreach ($legacyDocuments as $doc) {
            // Sesuai aturan: Abaikan jika id_bidang = 7
            if ($doc->id_bidang == 7) {
                continue;
            }

            $type = 'incoming';
            $senderOrReceiver = '';
            
            // id_kategori 1 = masuk, 2 = keluar
            if ($doc->id_kategori == 1) {
                $type = 'incoming';
                $senderOrReceiver = $doc->pengirim_surat ?? '-';
            } elseif ($doc->id_kategori == 2) {
                $type = 'outgoing';
                $senderOrReceiver = $doc->penerima_surat ?? '-';
            }

            // Temukan atau relasikan ID pengguna
            $created_by = null;
            if ($doc->id_user) {
                $legacyUser = DB::table('user')->where('id_user', $doc->id_user)->first();
                if ($legacyUser) {
                    $newUser = DB::table('users')->where('username', $legacyUser->username)->first();
                    if ($newUser) {
                        $created_by = $newUser->id;
                    }
                }
            }

            DB::table('documents')->updateOrInsert(
                ['reference_number' => $doc->no_arsip],
                [
                    'document_number' => $doc->no_surat ?? '-',
                    'type' => $type,
                    'document_date' => $doc->tanggal_surat ?? now(),
                    'received_date' => $doc->tanggal_upload,
                    'subject' => $doc->perihal ?? '-',
                    'sender_or_receiver' => $senderOrReceiver,
                    'file_path' => $doc->file_surat ? 'documents/' . $doc->file_surat : null,
                    'created_by' => $created_by,
                    'created_at' => $doc->tanggal_upload ? $doc->tanggal_upload . ' 00:00:00' : now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}