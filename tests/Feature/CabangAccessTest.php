<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CabangAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_cabang_only_sees_tickets_from_its_office(): void
    {
        $user = User::factory()->create(['role' => 'cabang', 'kode_kantor' => '001']);
        Ticket::create($this->ticketData('T-001', '001'));
        Ticket::create($this->ticketData('T-002', '002'));

        $this->actingAs($user)
            ->get(route('cabang.tickets'))
            ->assertOk()
            ->assertSee('T-001')
            ->assertDontSee('T-002');
    }

    public function test_cabang_ticket_always_uses_authenticated_users_office_code(): void
    {
        $user = User::factory()->create(['role' => 'cabang', 'kode_kantor' => '001']);

        $this->actingAs($user)->post(route('cabang.tickets.store'), [
            'nama_pelapor' => 'Pelapor Cabang',
            'email' => 'pelapor@example.test',
            'hp' => '08123456789',
            'kategori' => 'ATM',
            'tipe_pelapor' => 'Umum',
            'judul' => 'Gangguan ATM',
            'detail' => 'ATM tidak dapat digunakan.',
            'kode_kantor' => '999',
        ])->assertRedirect(route('cabang.tickets'));

        $this->assertDatabaseHas('tickets', [
            'email' => 'pelapor@example.test',
            'kode_kantor' => '001',
            'status' => 'open',
        ]);
    }

    public function test_cabang_cannot_access_admin_routes(): void
    {
        $user = User::factory()->create(['role' => 'cabang', 'kode_kantor' => '001']);

        $this->actingAs($user)->get('/admin/tickets')->assertForbidden();
    }

    private function ticketData(string $number, string $office): array
    {
        return [
            'nomor_tiket' => $number,
            'nama_pelapor' => 'Pelapor',
            'hp' => '08123456789',
            'email' => strtolower($number) . '@example.test',
            'kategori' => 'ATM',
            'judul' => 'Judul tiket',
            'detail' => 'Detail tiket',
            'status' => 'open',
            'kode_kantor' => $office,
        ];
    }
}
