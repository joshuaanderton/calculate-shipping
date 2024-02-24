<?php

namespace Ja\Shipping\Actions;

use EasyPost\EasyPostClient;

class BuyPostageLabelForOrder
{
    public static function run(string $orderId, string $shippingRateId): ?array
    {
        $client = new EasyPostClient(env('EASYPOST_API_KEY'));

        $shippingRate = $client->rate->retrieve($shippingRateId);

        $result = $client->order->buy($orderId, $shippingRate);

        return $result ?? null;
    }
}
