<?php

namespace Ja\Shipping\Actions;

use Ja\Shipping\Services\EasyPost;

/**
 * Buy a postage label using configured shipping service
 */
class BuyPostageLabelForShipment
{
    public static function run(string $shipmentId, string $rateId): ?array
    {
        $result = (new EasyPost)->shipmentBuy($shipmentId, $rateId);

        return $result ?? null;
    }
}
