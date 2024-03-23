<?php

namespace Ja\Shipping\Carriers;

class UPS extends Carrier {
    public string $id = 'ca_feac6b4eec3c4f7dbd80cb8079e3b02d';
    public string $name = 'UPS';
    public string $icon = '/images/shipping-carriers/ups.svg';
    public array $origins = ['US'];
    public bool $enabled = true;
}
