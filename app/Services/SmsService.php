<?php
namespace App\Services;

class SmsService
{
    private string $bridgeUrl;
    private string $secret;

    public function __construct()
    {
        // Read bridge config from .env
        $this->bridgeUrl = rtrim($_ENV['TERMUX_SMS_URL'] ?? '', '/');
        $this->secret    = $_ENV['TERMUX_SMS_SECRET'] ?? '';
    }

    public function sendSms(string $phoneNumber, string $message): bool
    {
        if (empty($this->bridgeUrl)) {
            error_log("SmsService: TERMUX_SMS_URL is not set in .env");
            return false;
        }

        $payload = json_encode([
            'secret'  => $this->secret,
            'phone'   => $phoneNumber,
            'message' => $message,
        ]);

        // Use PHP stream context to make an HTTP POST request
        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/json\r\nContent-Length: " . strlen($payload),
                'content' => $payload,
                'timeout' => 30,
            ],
        ]);

        $endpoint = $this->bridgeUrl;

        $response = @file_get_contents($endpoint, false, $context);

        if ($response === false) {
            error_log("SmsService: Could not connect to Termux bridge at $endpoint. Is the bridge running?");
            return false;
        }

        $data = json_decode($response, true);

        if (!empty($data['success'])) {
            return true;
        }

        error_log("SmsService: Bridge returned error: " . ($data['error'] ?? 'Unknown'));
        return false;
    }
}
