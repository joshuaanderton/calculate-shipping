<?php

namespace Ja\Shipping\Actions;

use EasyPost\EasyPostClient;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class Shipping
{
    public static function run(array $fromAddress, array $toAddress, array $parcels): Collection
    {
        $client = new EasyPostClient(env('EASYPOST_API_KEY'));

        try {
            if (count($parcels) === 1) {
                $shipment = $client->shipment->create([
                    'from_address' => $fromAddress,
                    'to_address' => $toAddress,
                    'parcel' => $parcels[0],
                ]);
            } else {
                $shipment = $client->order->create([
                    'from_address' => $fromAddress,
                    'to_address' => $toAddress,
                    'shipments' => collect($parcels)->map(fn ($parcel) => ['parcel' => $parcel])->toArray(),
                ]);
            }
        } catch (Exception $e) {
            Log::critical(implode(PHP_EOL, [
                "EasyPost Shipment/Order {$e->getMessage()}",
                './'.explode(base_path(), __FILE__)[1].':'.__LINE__,
            ]));
        }

        $rates = collect($shipment->rates);

        // Log any errors
        if ($rates->count() === 0) {
            collect($shipment->messages ?? [])
                ->filter(fn ($message) => $message->type === 'rate_error')
                ->each(fn ($message) => (
                    Log::critical(implode(PHP_EOL, [
                        "EasyPost Shipment/Order ({$message->carrier}): {$message->message}",
                        './'.explode(base_path(), __FILE__)[1].':'.__LINE__,
                    ]))
                ));
        }

        return
            $rates
                ->map(fn ($rate) => [
                    ...$rate->__toArray(),
                    'rate_int' => (int) ((float) $rate->rate) * 100,
                    'order_id' => $shipment->object === 'Order' ? $shipment->id : null,
                ])
                ->sortBy('rate_int')
                ->map(fn ($rate) => (object) $rate);
    }
}
