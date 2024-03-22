<?php

namespace Ja\Shipping\Actions;

use Illuminate\Support\Facades\App;
use Ja\Shipping\Services\EasyPost;

/**
 * Create a tracking object using configured shipping service
 */
class CreateTracking
{
    public static function run(string $trackingCode, string $carrier): array
    {
        if (App::environment('testing')) {
            $trackingCode = collect(EasyPost::testTrackingNumbers)->random();
        }

        $tracker = (new EasyPost)->trackerCreate($trackingCode, $carrier);

        return $tracker->__toArray();
    }
}
