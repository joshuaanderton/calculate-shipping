<?php

namespace Ja\Shipping\Carriers;

class LSO extends Carrier {
    public string $id = 'ca_cf6f870ac34f4fb58adb0d34b414420d';
    public string $name = 'LSO';
    public string $icon = '/images/shipping-carriers/lso.svg';
    public array $origins = ['US'];
    public bool $enabled = false;
}
