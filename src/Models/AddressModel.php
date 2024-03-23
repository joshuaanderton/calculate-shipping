<?php

namespace Ja\Shipping\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AddressModel extends Model
{
    public $table = 'addresses';

    protected $fillable = [
        'name',
        'phone',
        'city',
        'country',
        'line1',
        'line2',
        'postal_code',
        'state',
        'is_billing',
    ];

    protected $casts = ['is_billing' => 'boolean'];

    protected $appends = ['street1', 'street2', 'zip'];

    public static function booted(): void
    {
        static::saving(function ($address) {
            $address->postal_code = Str::upper($address->postal_code);
        });
    }

    public function street1(): Attribute
    {
        return new Attribute(
            get: fn (): ?string => $this->line1
        );
    }

    public function street2(): Attribute
    {
        return new Attribute(
            get: fn (): ?string => $this->line2
        );
    }

    public function zip(): Attribute
    {
        return new Attribute(
            get: fn (): ?string => $this->postal_code
        );
    }
}
