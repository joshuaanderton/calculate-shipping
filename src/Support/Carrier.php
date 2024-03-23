<?php

namespace Ja\Shipping\Support;

use Illuminate\Support\Collection;
use Ja\Shipping\Carriers;

class Carrier
{
    protected Collection $carriers;

    public function __construct()
    {
        $this->carriers = collect([
            'CanadaPost' => new Carriers\CanadaPost,
            'DHLExpress' => new Carriers\DHLExpress,
            'FedEx' => new Carriers\FedEx,
            'UPS' => new Carriers\UPS,
            'USPS' => new Carriers\USPS,
            'LSO' => new Carriers\LSO,
        ]);
    }

    public static function origins(): Collection
    {
        return (new static)->carriers->pluck('origins')->flatten()->unique();
    }

    public function whereOrigin(string|array $origin): Collection
    {
        return $this->carriers->filter(fn ($carrier) =>
            collect($origin)
                ->every(fn ($origin) => in_array($origin, $carrier->origins))
        );
    }

    public function find(string $key): ?Carriers\Carrier
    {
        return $this->carriers->get($key, null);
    }
}
