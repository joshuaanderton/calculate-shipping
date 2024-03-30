<?php

namespace Ja\Shipping\Actions\Shipments;

use App\Models\User;
use Illuminate\Http\Request;
use Ja\Shipping\Actions\CreateShipment;
use Ja\Shipping\Support\Facades\Carrier;
use Ja\Shipping\Support\Length;
use Ja\Shipping\Support\Weight;
use Ja\Shipping\Validators\ParcelDimensionsValidator;
use Lorisleiva\Actions\Concerns\AsAction;

class Store
{
    use AsAction;

    protected string $parcelAttributes = 'weight|weight_unit|length|length_unit|width|height';

    public function asController(Request $request)
    {
        $errors = ParcelDimensionsValidator::make($request->all(), [
            'postal_code' => 'required|string|max:140',
            'country' => 'required|string|in:'.Carrier::origins()->join(','),
            'currency' => 'required|string',
        ])->errors();

        $shipment = null;

        if ($errors->isEmpty()) {
            $shipment = $this->handle(
                user: $request->user(),
                zip: $request->postal_code,
                country: $request->country,
                dimensions: $request->only(explode('|', $this->parcelAttributes)),
                currency: $request->currency
            );
        }

        $errors = collect($errors)->concat($shipment['errors'] ?? [])->toArray();

        return response()->json(compact('shipment', 'errors'));
    }

    public function handle(User $user, string $zip, string $country, array $dimensions, string $currency)
    {
        $shop = $user->currentTeam->shop;
        $address = $shop->shippingFromAddress;
        $addressAttributes = explode('|', 'street1|street2|city|state|zip|country|phone');

        $fromAddress = (
            collect(['email' => $user->email, 'name' => $user->name])
                ->merge($address->only($addressAttributes))
                ->toArray()
        );

        $toAddress = [
            'email' => $user->email,
            'name' => __('Test Customer'),
            'zip' => $zip,
            'country' => $country,
        ];

        $dims = (object) $dimensions;

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
            fromAddress: $fromAddress,
            toAddress: $toAddress,
            parcel: $parcel->toArray(),
            carrierAccounts: Carrier::whereOrigin($fromAddress['country'])->pluck('id')->toArray(),
            options: [
                'currency' => $currency,
                'payment' => [
                    'type' => 'RECEIVER',
                    'account' => $shop->id,
                    'postal_code' => $toAddress['zip'],
                ]
            ]
        );

        $errors = (
            collect($shipment->messages)
                ->where('type', 'rate_error')
                ->map(function ($message) {
                    $content = str($message->message);
                    $field = $message->carrier;

                    if ($content->contains('is less than or equal to')) {
                        $field = $content->explode('shipment.parcel.')->slice(1)->first();
                        $field = str($field)->explode(': ensure this value is');
                        $max = str($field->last())->after('or equal to')->trim()->before(' and')->toString();
                        $field = $field->first();

                        $content = __('validation.max.numeric', ['attribute' => $field, 'max' => $max]);
                    }

                    return [$field => "{$message->carrier}: {$content}"];
                })
                ->collapse()
                ->toArray()
        );

        $rates = collect($shipment->rates)
            ->map(fn ($rate) => [
                ...$rate->__toArray(),
                'rate_int' => cents($rate->rate)
            ])
            ->sortBy('rate_int')
            ->values()
            ->toArray();

        return [
            'id' => $shipment->id,
            'rates' => $rates,
            'errors' => $errors,
        ];
    }
}
