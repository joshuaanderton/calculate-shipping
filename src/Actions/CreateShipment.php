<?php

namespace Ja\Shipping\Actions;

use EasyPost\Shipment;
use Ja\Shipping\Services\EasyPost;

class CreateShipment
{
    public static function run(array $fromAddress, array $toAddress, array $parcel): Shipment
    {
        return (new EasyPost)->shipmentCreate(
            $fromAddress,
            $toAddress,
            $parcel
        );
    }
}
