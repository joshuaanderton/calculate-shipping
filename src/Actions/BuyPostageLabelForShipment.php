<?php

namespace Ja\Shipping\Actions;

use EasyPost\EasyPostClient;

class BuyPostageLabelForShipment
{
    public static function run(string $shipmentId, string $shippingRateId): ?array
    {
        $client = new EasyPostClient(env('EASYPOST_API_KEY'));

        $result = $client->shipment->buy($shipmentId, [
            'rate' => $client->rate->retrieve($shippingRateId),
            'insurance' => null,
        ]);

        return $result ?? null;
    }
}
