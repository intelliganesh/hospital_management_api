<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WhatsappService
{

    private $phonwNumberId;
    private $token;
    private $verifyToken;

    /**
     * Summary of __construct
     */

    public function __construct()
    {
        $this->token         = config('services.whatsapp.token');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
        $this->verifyToken   = config('services.whatsapp.verify_token');
    }

    public function sendMessage(string $templateKey, string $phone, array $data)
    {
        $template = config("services.whatsapp.templates.$templateKey");

        if (! $template) {
            return [
                'success' => false,
                'message' => 'Template config not found',
            ];
        }

        if (! is_array($template) || ! isset($template['name'], $template['params']) || ! is_array($template['params'])) {
            return [
                'success' => false,
                'message' => 'Invalid WhatsApp template config',
            ];
        }

        $parameters = [];
        $defaultedParams = [];

        foreach ($template['params'] as $param) {
            $text = trim((string) ($data[$param] ?? ''));
            if ($text === '') {
                $text = 'N/A';
                $defaultedParams[] = $param;
            }

            $parameters[] = [
                'type' => 'text',
                'text' => $text,
            ];
        }

        if (! empty($defaultedParams)) {
            Log::warning('WhatsApp template params defaulted', [
                'template_key' => $templateKey,
                'params'       => $defaultedParams,
            ]);
        }

        Log::info('Sending WhatsApp template', [
            'template_key' => $templateKey,
            'template_name'=> $template['name'],
            'param_count'  => count($parameters),
            'to'           => $phone,
        ]);

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'                => $phone,
            'type'              => 'template',
            'template'          => [
                'name'       => $template['name'],
                'language'   => [
                    'code' => 'en',
                ],
                'components' => [
                    [
                        'type'       => 'body',
                        'parameters' => $parameters,
                    ],
                ],
            ],
        ];

        $url = sprintf(
            'https://graph.facebook.com/v25.0/%s/messages',
            $this->phoneNumberId
        );

        $response = Http::withToken($this->token)->post($url, $payload);

        if ($response->failed()) {

            Log::error('WhatsApp API Error', [
                'status'   => $response->status(),
                'response' => $response->json(),
            ]);

            return [
                'success' => false,
                'status'  => $response->status(),
                'data'    => $response->json(),
            ];
        }

        return [
            'success' => true,
            'status'  => $response->status(),
            'data'    => $response->json(),
        ];
    }
}
