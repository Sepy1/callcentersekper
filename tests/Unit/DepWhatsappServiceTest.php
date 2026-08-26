<?php

namespace Tests\Unit;

use App\Services\DepWhatsappService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DepWhatsappServiceTest extends TestCase
{
    public function test_it_sends_assignment_template_with_url_button(): void
    {
        config([
            'services.mahadata.endpoint' => 'https://example.test/messages',
            'services.mahadata.token' => 'test-token',
        ]);

        Http::fake([
            'https://example.test/messages' => Http::response(['messages' => [['id' => 'wamid.test']]], 200),
        ]);

        app(DepWhatsappService::class)->sendTemplateByName(
            '6285725681860',
            'ccs_tl',
            'en',
            ['Nama officer', 'TKT-001', 'ATM', 'Kartu tertelan'],
            'officer/tindak-lanjut?ticket_id=32'
        );

        Http::assertSent(function ($request) {
            return $request->url() === 'https://example.test/messages'
                && $request['to'] === '6285725681860'
                && $request['template']['name'] === 'ccs_tl'
                && $request['template']['language']['code'] === 'en'
                && $request['template']['components'][0]['parameters'][0]['text'] === 'Nama officer'
                && $request['template']['components'][0]['parameters'][3]['text'] === 'Kartu tertelan'
                && $request['template']['components'][1] === [
                    'type' => 'button',
                    'sub_type' => 'url',
                    'index' => '0',
                    'parameters' => [
                        ['type' => 'text', 'text' => 'officer/tindak-lanjut?ticket_id=32'],
                    ],
                ];
        });
    }
}
