<?php

namespace Ja\Shipping\Validators;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Ja\Shipping\Actions\CreateShipment;
use Ja\Shipping\Support\Facades\Carrier;
use Ja\Shipping\Support\Length;
use Ja\Shipping\Support\Weight;

/**
 * @resource based on EasyPost parcel requirements (https://www.easypost.com/docs/api#parcels)
 */
class ParcelDimensionsValidator
{
    public static function make(array $data, ?array $rules = [], ?string $shipFromCountry = null)
    {
        $weightUnits = collect(Weight::conversions())->keys()->join(',');
        $lengthUnits = collect(Length::conversions())->keys()->join(',');

        $errors = collect();
        if ($shipFromCountry) {
            $errors = static::verify($data, $shipFromCountry);
        }

        return Validator::make($data, array_merge([

            'weight' => [
                'required',
                'numeric',
                'min:0.1',
                'max:999999',
                fn ($attr, $value, $fail) =>
                    $errors->get($attr, false)
                        ? $fail($errors->get($attr, false))
                        : null
            ],

            'height' => [
                'nullable',
                'required_with:length,width',
                'numeric',
                'min:0.1',
                'max:999999',
                fn ($attr, $value, $fail) =>
                    $errors->get($attr, false)
                        ? $fail($errors->get($attr, false))
                        : null
            ],
            'width' => [
                'nullable',
                'required_with:length,height',
                'numeric',
                'min:0.1',
                'max:999999',
                fn ($attr, $value, $fail) =>
                    $errors->get($attr, false)
                        ? $fail($errors->get($attr, false))
                        : null
            ],
            'length' => [
                'nullable',
                'required_with:width,height',
                'numeric',
                'min:0.1',
                'max:999999',
                fn ($attr, $value, $fail) =>
                    $errors->get($attr, false)
                        ? $fail($errors->get($attr, false))
                        : null
            ],

            'weight_unit' => [
                'required_with:weight',
                'in:'.$weightUnits,
                fn ($attr, $value, $fail) =>
                    $errors->get($attr, false)
                        ? $fail($errors->get($attr, false))
                        : null
            ],
            'length_unit' => [
                'required_with:height,width,length',
                'in:'.$lengthUnits,
                fn ($attr, $value, $fail) =>
                    $errors->get($attr, false)
                        ? $fail($errors->get($attr, false))
                        : null
            ],

        ], $rules));
    }

    protected static function verify(array $data, string $country): Collection
    {
        $dims = (object) $data;

        $parcel = collect([
            'weight' => Weight::from($dims->weight, $dims->weight_unit)->toOz(),
        ]);

        if ($dims->length ?? null) {
            $parcel = $parcel->merge([
                'length' => Length::from($dims->length, $dims->length_unit)->toIn(),
                'width' => Length::from($dims->width, $dims->length_unit)->toIn(),
                'height' => Length::from($dims->height, $dims->length_unit)->toIn(),
            ]);
        }

        $shipment = CreateShipment::run(
            fromAddress: compact('country'),
            toAddress: compact('country'),
            parcel: $parcel->toArray(),
            carrierAccounts: Carrier::whereOrigin($country)->pluck('id')->toArray()
        );

        return collect($shipment->messages)
            ->where('type', 'rate_error')
            ->filter(fn ($message) => str($message->message)->contains('shipment.parcel.'))
            ->map(function ($message) {

                $content = str($message->message);

                $field = $content->explode('shipment.parcel.')->slice(1)->first();
                $field = str($field)->explode(': ensure this value is');

                if ($content->contains('is less than or equal to')) {
                    $max = str($field->last())->after('or equal to')->trim()->before(' and')->toString();
                    $field = $field->first();
                    $content = __('validation.max.numeric', ['attribute' => $field, 'max' => $max]);
                }

                return [$field => "{$message->carrier}: {$content}"];
            })
            ->collapse();
    }
}
