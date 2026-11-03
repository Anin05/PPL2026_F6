<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['code' => 'CLI-001', 'name' => 'PT Maju Jaya',    'address' => 'Jl. Asia Afrika No. 10', 'city' => 'Bandung', 'phone' => '022-1234567', 'mobile' => '081234567890', 'email' => 'info@majujaya.co.id'],
            ['code' => 'CLI-002', 'name' => 'CV Sinar Abadi',  'address' => 'Jl. Dago No. 25',        'city' => 'Bandung', 'phone' => '022-7654321', 'mobile' => '081298765432', 'email' => 'kontak@sinarabadi.co.id'],
            ['code' => 'CLI-003', 'name' => 'PT Nusantara Tech','address' => 'Jl. Sudirman No. 5',    'city' => 'Jakarta', 'phone' => '021-5550123', 'mobile' => '081355500123', 'email' => 'hello@nusantaratech.id'],
        ];

        foreach ($data as $row) {
            Client::firstOrCreate(['code' => $row['code']], $row + ['status' => 'active']);
        }
    }
}