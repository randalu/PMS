<?php

namespace App\Services;

use App\Models\Setting;
use App\Support\SriLankanPhone;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SmsService
{
    public function isEnabled(): bool
    {
        return filter_var(Setting::getValue('sms_enabled', config('services.smslenz.enabled') ? '1' : '0'), FILTER_VALIDATE_BOOLEAN);
    }

    public function send(string $phone, string $message): bool
    {
        if (! $this->isEnabled()) {
            Log::info('SMS skipped because SMS is disabled.', ['phone' => $phone]);

            return false;
        }

        $contact = SriLankanPhone::normalize($phone);
        if ($contact === null) {
            throw new RuntimeException('Phone number must be a valid Sri Lankan mobile number.');
        }

        $message = str($message)->limit(621, '')->toString();
        $userId = config('services.smslenz.user_id');
        $apiKey = config('services.smslenz.api_key');
        $senderId = Setting::getValue('sms_sender_id', (string) config('services.smslenz.sender_id'));

        if (! $userId || ! $apiKey || ! $senderId) {
            throw new RuntimeException('SMSlenz credentials are not configured.');
        }

        $response = Http::timeout(15)->asForm()->post(rtrim((string) config('services.smslenz.base_url'), '/').'/send-sms', [
            'user_id' => $userId,
            'api_key' => $apiKey,
            'sender_id' => $senderId,
            'contact' => $contact,
            'message' => $message,
        ]);

        if ($response->failed()) {
            Log::warning('SMSlenz send failed.', [
                'phone' => $contact,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        }

        return true;
    }
}
