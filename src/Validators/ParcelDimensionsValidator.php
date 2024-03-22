<?php

namespace Ja\Shipping\Validators;

use Illuminate\Support\Facades\Validator;
use Ja\Shipping\Support\Length;
use Ja\Shipping\Support\Weight;

/**
 * @resource based on EasyPost parcel requirements (https://www.easypost.com/docs/api#parcels)
 */
class ParcelDimensionsValidator
{
    public static function make(array $data, ?array $rules = [])
    {
        $weightUnits = collect(Weight::conversions())->keys()->join(',');
        $lengthUnits = collect(Length::conversions())->keys()->join(',');

        return Validator::make($data, array_merge([

            'weight' => 'required|numeric|min:0.1',

            'height' => 'nullable|required_with:length,width|numeric|min:0.1',
            'width' => 'nullable|required_with:length,height|numeric|min:0.1',
            'length' => 'nullable|required_with:width,height|numeric|min:0.1',

            'weight_unit' => 'required_with:weight|in:'.$weightUnits,
            'length_unit' => 'required_with:height,width,length|in:'.$lengthUnits,

        ], $rules));
    }
}
