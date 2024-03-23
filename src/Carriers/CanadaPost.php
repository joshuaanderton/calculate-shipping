<?php

namespace Ja\Shipping\Carriers;

class CanadaPost extends Carrier {
    public string $id = 'ca_8eef23208afa469a9f29f4c222ea791c';
    public string $name = 'Canada Post';
    public string $icon = '/images/shipping-carriers/canadapost.svg';
    public array $origins = ['CA'];
    public bool $enabled = true;
}
