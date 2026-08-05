<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DailyService
{
    private $apiKey;
    private $domain;

    /**
     * Summary of __construct
     */

    public function __construct()
    {
        $this->apiKey = config('services.daily.key');
        $this->domain = config('services.daily.domain');
    }

    public function createRoom($appointment_number, $appointment_datetime): array
    {
        try {

            $room = 'CONSULTATION-' . $appointment_number;

            $exp = \Carbon\Carbon::parse($appointment_datetime)
                ->addMinutes(60)
                ->timestamp;

            $checkResponse = Http::withToken($this->apiKey)
                ->get($this->domain . 'rooms/' . $room);

            $roomData = $checkResponse->json();

            // Room does not exist
            if (
                ! $checkResponse->successful() ||
                isset($roomData['error'])
            ) {

                $createResponse = Http::withToken($this->apiKey)
                    ->post($this->domain . 'rooms', [
                        'name'       => $room,
                        'privacy'    => 'private',
                        'properties' => [
                            'enable_prejoin_ui'            => true,
                            'enable_noise_cancellation_ui' => true,
                            'enable_knocking'              => true,
                            'enable_screenshare'           => true,
                            'enable_video_processing_ui'   => true,
                            'start_video_off'              => true,
                            'start_audio_off'              => true,
                            // 'exp' => $exp,
                            'enable_recording'             => 'cloud',
                        ],
                    ]);

                if ($createResponse->failed()) {

                    \Log::error('Daily Room Creation Failed', [
                        'status'   => $createResponse->status(),
                        'response' => $createResponse->json(),
                    ]);

                    return [
                        'success' => false,
                        'message' => 'Unable to create Daily room',
                        'data'    => $createResponse->json(),
                    ];
                }

                return [
                    'success' => true,
                    'data'    => $createResponse->json(),
                ];
            }

            // Existing room
            return [
                'success' => true,
                'data'    => $roomData,
            ];

        } catch (\Exception $e) {

            \Log::error('Daily Room Exception', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function createDoctorToken($room)
    {
        return Http::withToken($this->apiKey)->post($this->domain . 'meeting-tokens',
            [
                'properties' => [
                    'room_name' => $room,
                    'is_owner'  => true,
                ],
            ]
        )->json();
    }

    public function createPatientToken($room)
    {
        return Http::withToken($this->apiKey)->post($this->domain . 'meeting-tokens',
            [
                'properties' => [
                    'room_name' => $room,
                    'is_owner'  => false,
                ],
            ]
        )->json();
    }

    public function BatchRoomDelete(array $rooms)
    {
        return Http::withToken($this->apiKey)->delete($this->domain . 'batch/rooms',
            [
                'room_names' => $rooms,
            ]
        )->json();

    }
}
