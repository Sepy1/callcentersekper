<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\DepWhatsappService;
use Illuminate\Support\Facades\Log;

class SendWhatsappTemplate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $phone;
    public string $templateId;
    public string $language;
    public array $params;
    public ?int $ticketId;
    public ?string $buttonUrlParameter;

    public function __construct(string $phone, string $templateId, string $language = 'id', array $params = [], ?int $ticketId = null, ?string $buttonUrlParameter = null)
    {
        $this->phone = $phone;
        $this->templateId = $templateId;
        $this->language = $language;
        $this->params = $params;
        $this->ticketId = $ticketId;
        $this->buttonUrlParameter = $buttonUrlParameter;
        // put on notifications queue by default
        $this->onQueue('notifications');
    }

    public function handle(DepWhatsappService $dep)
    {
        try {
            $dep->sendTemplateById($this->phone, $this->templateId, $this->language, $this->params, $this->buttonUrlParameter);
            Log::info('TICKET WHATSAPP SENT', [
                'ticket_id' => $this->ticketId,
                'template' => $this->templateId,
                'phone_suffix' => substr($this->phone, -4),
            ]);
        } catch (\Throwable $e) {
            Log::error('TICKET WHATSAPP FAILED', [
                'ticket_id' => $this->ticketId,
                'template' => $this->templateId,
                'phone_suffix' => substr($this->phone, -4),
                'error' => $e->getMessage(),
            ]);
            // let the job fail and be retried according to worker settings
            throw $e;
        }
    }
}
