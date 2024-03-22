<?php

namespace Ja\Shipping\Actions;

use EasyPost\Shipment;
use Ja\Shipping\Services\EasyPost;

/**
 * Create a shipment using configured shipping service
 */
class CreateShipment
{
    public static function run(array $fromAddress, array $toAddress, array $parcel, ?string $currency = null): Shipment
    {
        return (new EasyPost)->shipmentCreate(
            $fromAddress,
            $toAddress,
            $parcel,
            $currency
        );
    }
}
