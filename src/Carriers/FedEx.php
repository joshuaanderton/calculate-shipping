<?php

namespace Ja\Shipping\Carriers;

class FedEx extends Carrier {
    public string $id = 'ca_6cd6e704f6104a048df8182b14680f1a';
    public string $name = 'FedEx';
    public string $icon = '/images/shipping-carriers/fedex.svg';
    public array $origins = ['US'];
    public bool $enabled = true;
}
