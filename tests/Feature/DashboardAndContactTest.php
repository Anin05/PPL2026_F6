<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Contact;
use App\Models\Interaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAndContactTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $client = Client::create([
            'code' => 'CLI-TEST',
            'name' => 'PT Testing Indonesia',
            'city' => 'Jakarta',
            'status' => 'active',
        ]);

        Contact::create([
            'client_id' => $client->id,
            'name' => 'Budi PIC',
            'position' => 'Manager',
            'type' => 'PIC',
            'phone' => '08123456789',
            'email' => 'budi@test.com',
            'status' => 'active',
        ]);

        Contact::create([
            'client_id' => $client->id,
            'name' => 'Dewi Direktur',
            'position' => 'Direktur Utama',
            'type' => 'Direktur',
            'phone' => '08198765432',
            'email' => 'dewi@test.com',
            'status' => 'active',
        ]);

        Interaction::create([
            'client_id' => $client->id,
            'type' => 'Telepon',
            'notes' => 'Tindak lanjut negosiasi kontrak.',
            'interaction_at' => now(),
        ]);
    }

    public function test_dashboard_api_returns_correct_stats(): void
    {
        $response = $this->getJson('/api/v1/dashboard');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'total_clients' => 1,
                    'active_clients' => 1,
                    'total_contacts' => 2,
                    'pic_count' => 1,
                    'dir_count' => 1,
                    'follow_ups' => 1,
                ],
            ]);

        $this->assertNotEmpty($response->json('data.recent_interactions'));
    }

    public function test_contacts_api_returns_list_and_stats(): void
    {
        $response = $this->getJson('/api/v1/contacts');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'meta' => [
                    'total' => 2,
                    'stats' => [
                        'total' => 2,
                        'pic_count' => 1,
                        'dir_count' => 1,
                    ],
                ],
            ]);
    }

    public function test_contacts_api_filters_by_type(): void
    {
        $response = $this->getJson('/api/v1/contacts?type=PIC');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Budi PIC', $data[0]['name']);
    }

    public function test_contacts_api_create_contact(): void
    {
        $client = Client::first();

        $payload = [
            'client_id' => $client->id,
            'name' => 'Siti Aminah',
            'position' => 'Staff Keuangan',
            'type' => 'PIC',
            'phone' => '08551234567',
            'email' => 'siti@test.com',
        ];

        $response = $this->postJson('/api/v1/contacts', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Kontak berhasil ditambahkan.',
            ]);

        $this->assertDatabaseHas('contacts', [
            'name' => 'Siti Aminah',
            'email' => 'siti@test.com',
            'client_id' => $client->id,
        ]);
    }

    public function test_contacts_api_update_and_status(): void
    {
        $contact = Contact::where('name', 'Budi PIC')->first();

        $updateResponse = $this->putJson("/api/v1/contacts/{$contact->id}", [
            'position' => 'Senior Manager',
        ]);

        $updateResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Kontak berhasil diperbarui.',
            ]);

        $this->assertEquals('Senior Manager', $contact->fresh()->position);

        // Test status toggle
        $patchResponse = $this->patchJson("/api/v1/contacts/{$contact->id}/status");
        $patchResponse->assertStatus(200);
        $this->assertEquals('archived', $contact->fresh()->status);
    }

    public function test_web_routes_render_successfully(): void
    {
        $this->get('/dashboard')->assertStatus(200);
        $this->get('/kontak')->assertStatus(200);
        $this->get('/kontak/tambah')->assertStatus(200);

        $contact = Contact::first();
        $this->get("/kontak/{$contact->id}/edit")->assertStatus(200);

        $this->get('/riwayat')->assertStatus(200);
        $this->get('/riwayat/tambah')->assertStatus(200);
        $this->get('/arsip')->assertStatus(200);
    }

    public function test_interactions_api_list_and_create(): void
    {
        $client = Client::first();

        $response = $this->getJson('/api/v1/interactions');
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'meta' => [
                    'stats' => [
                        'all_count' => 1,
                    ],
                ],
            ]);

        // Create new interaction
        $createRes = $this->postJson('/api/v1/interactions', [
            'client_id' => $client->id,
            'type' => 'Email',
            'notes' => 'Pengiriman proposal awal.',
            'interaction_at' => now()->format('Y-m-d H:i:s'),
        ]);

        $createRes->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Interaksi berhasil dicatat.',
            ]);

        $this->assertDatabaseHas('interactions', [
            'client_id' => $client->id,
            'notes' => 'Pengiriman proposal awal.',
        ]);
    }

    public function test_client_archive_and_restore(): void
    {
        $client = Client::first();

        // Archive client
        $archiveRes = $this->patchJson("/api/v1/clients/{$client->id}/status", ['status' => 'archived']);
        $archiveRes->assertStatus(200);
        $this->assertEquals('archived', $client->fresh()->status);

        // Restore client
        $restoreRes = $this->patchJson("/api/v1/clients/{$client->id}/status", ['status' => 'active']);
        $restoreRes->assertStatus(200);
        $this->assertEquals('active', $client->fresh()->status);
    }
}

