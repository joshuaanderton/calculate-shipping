<?php

namespace Ja\Shipping\Actions;

use Illuminate\Support\Collection;
use Ja\Shipping\Carriers;
use Lorisleiva\Actions\Concerns\AsObject;

class ShippingCarriers
{
    use AsObject;

    public function handle(): Collection
    {
        return collect([
            'CanadaPost' => new Carriers\CanadaPost,
            'DHLExpress' => new Carriers\DHLExpress,
            'FedEx' => new Carriers\FedEx,
            'UPS' => new Carriers\UPS,
            'USPS' => new Carriers\USPS,
            'LSO' => new Carriers\LSO,
        ]);
    }

    public static function whereOrigin(string|array $origin): Collection
    {
        return static::run()->filter(fn ($carrier) => in_array($origin, $carrier->origins));
    }
}
