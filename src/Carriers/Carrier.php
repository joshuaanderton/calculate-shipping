<?php

namespace Ja\Shipping\Carriers;

abstract class Carrier
{
    public string $id;

    public string $name;

    public string $icon;

    public array $origins;

    public bool $enabled;
}
