<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'client' => ['code' => 'CLI-001', 'name' => 'PT Maju Jaya', 'address' => 'Jl. Asia Afrika No. 10', 'city' => 'Bandung', 'phone' => '022-1234567', 'mobile' => '081234567890', 'email' => 'info@majujaya.co.id'],
                'contacts' => [
                    ['name' => 'Budi Santoso', 'position' => 'Manajer Pengadaan', 'type' => 'PIC', 'phone' => '081211110001', 'email' => 'budi@majujaya.co.id'],
                    ['name' => 'Rina Wijaya', 'position' => 'Direktur Utama', 'type' => 'Direktur', 'phone' => '081211110002', 'email' => 'rina@majujaya.co.id'],
                ],
                'interactions' => [
                    ['type' => 'Meeting', 'notes' => 'Pembahasan kontrak kerja sama tahun 2026.', 'interaction_at' => '2026-10-20 10:00:00'],
                    ['type' => 'Telepon', 'notes' => 'Konfirmasi jadwal pengiriman.', 'interaction_at' => '2026-10-28 14:30:00'],
                ],
            ],
            [
                'client' => ['code' => 'CLI-002', 'name' => 'CV Sinar Abadi', 'address' => 'Jl. Dago No. 25', 'city' => 'Bandung', 'phone' => '022-7654321', 'mobile' => '081298765432', 'email' => 'kontak@sinarabadi.co.id'],
                'contacts' => [
                    ['name' => 'Agus Pratama', 'position' => 'Staf Keuangan', 'type' => 'PIC', 'phone' => '081222220001', 'email' => 'agus@sinarabadi.co.id'],
                ],
                'interactions' => [
                    ['type' => 'Email', 'notes' => 'Pengiriman penawaran harga.', 'interaction_at' => '2026-10-25 09:15:00'],
                ],
            ],
            [
                'client' => ['code' => 'CLI-003', 'name' => 'PT Nusantara Tech', 'address' => 'Jl. Sudirman No. 5', 'city' => 'Jakarta', 'phone' => '021-5550123', 'mobile' => '081355500123', 'email' => 'hello@nusantaratech.id'],
                'contacts' => [
                    ['name' => 'Dewi Lestari', 'position' => 'Project Manager', 'type' => 'PIC', 'phone' => '081233330001', 'email' => 'dewi@nusantaratech.id'],
                    ['name' => 'Hendra Kusuma', 'position' => 'Direktur Operasional', 'type' => 'Direktur', 'phone' => '081233330002', 'email' => 'hendra@nusantaratech.id'],
                ],
                'interactions' => [
                    ['type' => 'Kunjungan', 'notes' => 'Kunjungan ke kantor klien untuk demo sistem.', 'interaction_at' => '2026-10-30 13:00:00'],
                    ['type' => 'WhatsApp', 'notes' => 'Tindak lanjut hasil demo.', 'interaction_at' => '2026-11-01 11:20:00'],
                ],
            ],
        ];

        foreach ($data as $row) {
            $client = Client::firstOrCreate(
                ['code' => $row['client']['code']],
                $row['client'] + ['status' => 'active']
            );

            foreach ($row['contacts'] as $contact) {
                $client->contacts()->firstOrCreate(
                    ['email' => $contact['email']],
                    $contact + ['status' => 'active']
                );
            }

            foreach ($row['interactions'] as $interaction) {
                $client->interactions()->firstOrCreate(
                    ['interaction_at' => $interaction['interaction_at'], 'type' => $interaction['type']],
                    $interaction
                );
            }
        }
    }
}