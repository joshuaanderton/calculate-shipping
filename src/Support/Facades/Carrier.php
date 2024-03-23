<?php

namespace Ja\Shipping\Support\Facades;

use Illuminate\Support\Facades\Facade;

class Carrier extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'carrier';
    }
}
