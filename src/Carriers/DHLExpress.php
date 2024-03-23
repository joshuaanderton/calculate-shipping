<?php

namespace Ja\Shipping\Carriers;

class DHLExpress extends Carrier {
    public string $id = 'ca_bf38cc5dde504d9cbded2a7832162f8c';
    public string $name = 'DHL Express';
    public string $icon = '/images/shipping-carriers/dhl.svg';
    public array $origins = ['US'];
    public bool $enabled = true;
}
