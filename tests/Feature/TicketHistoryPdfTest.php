<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketHistoryPdfTest extends TestCase
{
    use RefreshDatabase;

    /** @dataProvider permittedRoles */
    public function test_permitted_user_can_download_ticket_history_pdf(string $role): void
    {
        $user = User::factory()->create(['role' => $role]);
        $ticket = Ticket::create([
            'nomor_tiket' => 'PDF-001',
            'nama_pelapor' => 'Pelapor Test',
            'kategori' => 'Pengaduan',
            'judul' => 'Test PDF',
            'detail' => 'Detail test',
            'status' => 'open',
        ]);

        if ($role === 'officer') {
            $ticket->officers()->attach($user->id, ['status' => 'assigned']);
        }

        $response = $this->actingAs($user)->get(route('tickets.history.pdf', $ticket));

        $response->assertOk();
        $this->assertStringContainsString(
            'attachment; filename=riwayat-tiket-PDF-001.pdf',
            (string) $response->headers->get('content-disposition')
        );
    }

    public static function permittedRoles(): array
    {
        return [['admin'], ['qa'], ['officer']];
    }
}
