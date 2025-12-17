<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kategori = ['Teknis', 'Layanan', 'Aduan', 'Informasi'];
        $status = ['open', 'in_progress', 'closed'];
        $tipe_pelapor = ['nasabah', 'non-nasabah'];
        $media_closing = ['email', 'telepon', 'whatsapp', 'aplikasi'];
        for ($i = 1; $i <= 100; $i++) {
            DB::table('tickets')->insert([
                'nomor_tiket' => 'TIKET-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'nama_pelapor' => 'Pelapor ' . $i,
                'hp' => '0812' . rand(10000000, 99999999),
                'email' => 'pelapor' . $i . '@mail.com',
                'kategori' => $kategori[array_rand($kategori)],
                'judul' => 'Judul Tiket ' . $i,
                'detail' => 'Detail tiket ke-' . $i,
                'closing_notes' => 'Tindak lanjut tiket ke-' . $i,
                'qa_summary' => 'QA summary tiket ke-' . $i,
                'status' => $status[array_rand($status)],
                'officer' => 'officer' . rand(1, 3),
                'waktu_eskalasi' => Carbon::now()->addHours(rand(1, 72)),
                'created_at' => Carbon::now()->subDays(rand(0, 10)),
                'updated_at' => Carbon::now(),
                'tipe_pelapor' => $tipe_pelapor[array_rand($tipe_pelapor)],
                'is_nasabah' => rand(0, 1),
                'id_ktp' => Str::random(16),
                'nomor_rekening' => rand(1000000000, 9999999999),
                'nama_ibu' => 'Ibu ' . $i,
                'alamat' => 'Alamat pelapor ' . $i,
                'tempat_lahir' => 'Kota ' . rand(1, 10),
                'tgl_lahir' => Carbon::now()->subYears(rand(20, 50))->subDays(rand(0, 365)),
                'kode_kantor' => 'KTR' . rand(100, 999),
                'upload_ktp' => 'ktp_' . $i . '.jpg',
                'upload_bukti' => 'bukti_' . $i . '.jpg',
                'media_closing' => $media_closing[array_rand($media_closing)],
            ]);
        }
    }
}
