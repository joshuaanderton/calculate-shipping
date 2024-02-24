<?php

namespace Ja\Shipping\Actions;

use EasyPost\EasyPostClient;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Ja\Shipping\Services\EasyPost;

class RetrieveShippingRates
{
    public static function run(array $fromAddress, array $toAddress, array $parcels): Collection
    {
        try {
            $shipment = (new EasyPost)->orderOrShipmentCreate(
                $fromAddress,
                $toAddress,
                $parcels
            );
        } catch (Exception $e) {
            Log::critical(implode(PHP_EOL, [
                "Shipping Calculator Error: {$e->getMessage()}",
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
                        "Shipping Calculator Error: ({$message->carrier}): {$message->message}",
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
