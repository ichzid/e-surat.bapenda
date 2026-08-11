<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Disposition;
use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $departments = collect([
            'SEKRETARIAT',
            'KASUBBAG UMUM',
            'BIDANG PERENCANAAN DAN PENGEMBANGAN',
            'BIDANG PELAYANAN DAN PENETAPAN',
            'BIDANG PENDATAAN DAN PENILAIAN',
            'BIDANG PENAGIHAN KEBERATAN DAN PELAPORAN',
        ])->mapWithKeys(fn ($name) => [$name => Department::firstOrCreate(['name' => $name])]);

        // ========== ADMIN ==========
        $admin = User::updateOrCreate(['username' => 'admin'], [
            'name'     => 'Administrator',
            'password' => Hash::make('123456'),
            'role'     => 'administrator',
        ]);

        // ========== OPERATOR SEKRETARIAT (input surat masuk) ==========
        $operatorSekretariat = User::updateOrCreate(['username' => 'surat'], [
            'name'          => 'Operator Sekretariat',
            'password'      => Hash::make('123456'),
            'role'          => 'operator',
            'department_id' => $departments['SEKRETARIAT']->id,
        ]);

        // ========== OPERATOR BIDANG LAIN ==========
        User::updateOrCreate(['username' => 'kasubbag_umum'], [
            'name'          => 'Kasubbag Umum',
            'password'      => Hash::make('123456'),
            'role'          => 'operator',
            'department_id' => $departments['KASUBBAG UMUM']->id,
        ]);

        $operatorPerencanaan = User::updateOrCreate(['username' => 'op_perencanaan'], [
            'name'          => 'Operator Perencanaan & Pengembangan',
            'password'      => Hash::make('123456'),
            'role'          => 'operator',
            'department_id' => $departments['BIDANG PERENCANAAN DAN PENGEMBANGAN']->id,
        ]);

        User::updateOrCreate(['username' => 'op_pelayanan'], [
            'name'          => 'Operator Pelayanan & Penetapan',
            'password'      => Hash::make('123456'),
            'role'          => 'operator',
            'department_id' => $departments['BIDANG PELAYANAN DAN PENETAPAN']->id,
        ]);

        User::updateOrCreate(['username' => 'op_pendataan'], [
            'name'          => 'Operator Pendataan & Penilaian',
            'password'      => Hash::make('123456'),
            'role'          => 'operator',
            'department_id' => $departments['BIDANG PENDATAAN DAN PENILAIAN']->id,
        ]);

        User::updateOrCreate(['username' => 'op_penagihan'], [
            'name'          => 'Operator Penagihan, Keberatan & Pelaporan',
            'password'      => Hash::make('123456'),
            'role'          => 'operator',
            'department_id' => $departments['BIDANG PENAGIHAN KEBERATAN DAN PELAPORAN']->id,
        ]);

        // ========== SEKRETARIS ==========
        $sekretaris = User::updateOrCreate(['username' => 'sekretaris'], [
            'name'          => 'Sekretaris',
            'password'      => Hash::make('123456'),
            'role'          => 'sekretaris',
            'department_id' => $departments['SEKRETARIAT']->id,
        ]);

        // ========== KEPALA BADAN ==========
        User::updateOrCreate(['username' => 'kaban'], [
            'name'     => 'Kepala Badan',
            'password' => Hash::make('123456'),
            'role'     => 'kepala_badan',
        ]);

        // ========== SAMPLE DOCUMENTS ==========
        Storage::disk('public')->put('documents/contoh-dokumen-seed.pdf', "%PDF-1.4\n1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 300 144] /Contents 4 0 R >>\nendobj\n4 0 obj\n<< /Length 44 >>\nstream\nBT /F1 12 Tf 40 80 Td (Contoh Dokumen E-Surat) Tj ET\nendstream\nendobj\nxref\n0 5\n0000000000 65535 f \n0000000009 00000 n \n0000000058 00000 n \n0000000115 00000 n \n0000000204 00000 n \ntrailer\n<< /Root 1 0 R /Size 5 >>\nstartxref\n298\n%%EOF");

        $incomingPending = Document::updateOrCreate(['document_number' => 'SM-001/BAPENDA/I/2026'], [
            'type' => 'incoming',
            'document_date' => now()->subDays(5)->toDateString(),
            'received_date' => now()->subDays(4)->toDateString(),
            'sender_or_receiver' => 'Sekretariat Daerah Kabupaten Batu Bara',
            'subject' => 'Permintaan data realisasi pendapatan daerah',
            'department_id' => $departments['SEKRETARIAT']->id,
            'status' => 'menunggu_disposisi',
            'file_path' => 'documents/contoh-dokumen-seed.pdf',
            'created_by' => $operatorSekretariat->id,
        ]);

        $incomingDone = Document::updateOrCreate(['document_number' => 'SM-002/BAPENDA/I/2026'], [
            'type' => 'incoming',
            'document_date' => now()->subDays(3)->toDateString(),
            'received_date' => now()->subDays(2)->toDateString(),
            'sender_or_receiver' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            'subject' => 'Penyampaian laporan koordinasi pendapatan',
            'department_id' => $departments['SEKRETARIAT']->id,
            'status' => 'selesai',
            'file_path' => 'documents/contoh-dokumen-seed.pdf',
            'created_by' => $operatorSekretariat->id,
        ]);

        $incomingDispositioned = Document::updateOrCreate(['document_number' => 'SM-003/BAPENDA/I/2026'], [
            'type' => 'incoming',
            'document_date' => now()->subDays(7)->toDateString(),
            'received_date' => now()->subDays(6)->toDateString(),
            'sender_or_receiver' => 'Kantor Pelayanan Pajak Pratama Kisaran',
            'subject' => 'Koordinasi pendataan wajib pajak daerah',
            'department_id' => $departments['SEKRETARIAT']->id,
            'status' => 'sudah_disposisi',
            'file_path' => 'documents/contoh-dokumen-seed.pdf',
            'created_by' => $operatorSekretariat->id,
        ]);

        Document::updateOrCreate(['document_number' => 'SK-001/BAPENDA/I/2026'], [
            'type' => 'outgoing',
            'document_date' => now()->subDays(2)->toDateString(),
            'received_date' => now()->subDay()->toDateString(),
            'sender_or_receiver' => 'Sekretariat Daerah Kabupaten Batu Bara',
            'subject' => 'Penyampaian data target pendapatan daerah',
            'department_id' => $departments['SEKRETARIAT']->id,
            'status' => 'selesai',
            'file_path' => 'documents/contoh-dokumen-seed.pdf',
            'created_by' => $operatorSekretariat->id,
        ]);

        Document::updateOrCreate(['document_number' => 'SK-002/BAPENDA/I/2026'], [
            'type' => 'outgoing',
            'document_date' => now()->subDay()->toDateString(),
            'received_date' => now()->toDateString(),
            'sender_or_receiver' => 'BPKAD Kabupaten Batu Bara',
            'subject' => 'Undangan rapat evaluasi pendapatan daerah',
            'department_id' => $departments['SEKRETARIAT']->id,
            'status' => 'selesai',
            'file_path' => 'documents/contoh-dokumen-seed.pdf',
            'created_by' => $operatorSekretariat->id,
        ]);

        Disposition::updateOrCreate([
            'document_id' => $incomingDispositioned->id,
            'department_id' => $departments['BIDANG PERENCANAAN DAN PENGEMBANGAN']->id,
            'target_role' => 'department',
        ], [
            'created_by' => $sekretaris->id,
            'note' => 'Untuk ditindaklanjuti — Koordinasikan data pendukung dengan bidang terkait.',
            'follow_up_status' => 'diproses',
            'follow_up_note' => 'Sedang dilakukan pengecekan data awal.',
            'followed_up_at' => now(),
        ]);

        Disposition::updateOrCreate([
            'document_id' => $incomingDispositioned->id,
            'target_role' => 'kepala_badan',
        ], [
            'created_by' => $sekretaris->id,
            'note' => 'Untuk diketahui — Surat sudah diteruskan ke bidang terkait.',
        ]);
    }
}
