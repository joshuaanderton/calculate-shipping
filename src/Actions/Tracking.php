<?php

namespace Ja\Shipping\Actions;

use EasyPost\EasyPostClient;

class Tracking
{
    public static function run(string $trackingCode, string $carrier): array
    {
        $client = new EasyPostClient(env('EASYPOST_API_KEY'));

        // Test tracking numbers: EZ1000000001, EZ2000000002, EZ7000000007
        $tracker = $client->tracker->create([
            'tracking_code' => $trackingCode,
            'carrier' => $carrier,
        ]);

        return $tracker->__toArray();
    }
}
