<?php

namespace Ja\Shipping\Actions;

use Ja\Shipping\Services\EasyPost;

class BuyPostageLabelForShipment
{
    public static function run(string $shipmentId, string $shippingRateId): ?array
    {
        $result = (new EasyPost)->shipmentBuy($shipmentId, $shippingRateId);

        return $result ?? null;
    }
}
