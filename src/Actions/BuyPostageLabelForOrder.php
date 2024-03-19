<?php

namespace Ja\Shipping\Actions;

use Ja\Shipping\Services\EasyPost;

class BuyPostageLabelForOrder
{
    public static function run(string $orderId, string $shippingRateId): ?array
    {
        $easyPost = new EasyPost;

        $shippingRate = $easyPost->rateRetrieve($shippingRateId);

        $result = $easyPost->orderBuy($orderId, $shippingRate);

        return $result ?? null;
    }
}
