<?php

namespace Ja\Shipping\Carriers;

class USPS extends Carrier {
    public string $id = 'ca_4fa26545dc084547b7bdbc32811914e8';
    public string $name = 'USPS';
    public string $icon = '/images/shipping-carriers/usps.svg';
    public array $origins = ['US'];
    public bool $enabled = true;
}
