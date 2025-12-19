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

    public function __construct(string $phone, string $templateId, string $language = 'id', array $params = [])
    {
        $this->phone = $phone;
        $this->templateId = $templateId;
        $this->language = $language;
        $this->params = $params;
        // put on notifications queue by default
        $this->onQueue('notifications');
    }

    public function handle(DepWhatsappService $dep)
    {
        try {
            $dep->sendTemplateById($this->phone, $this->templateId, $this->language, $this->params);
            Log::info('SendWhatsappTemplate: dispatched', ['phone' => $this->phone, 'template' => $this->templateId]);
        } catch (\Throwable $e) {
            Log::error('SendWhatsappTemplate failed', ['err' => $e->getMessage(), 'phone' => $this->phone]);
            // let the job fail and be retried according to worker settings
            throw $e;
        }
    }
}
