<?php

namespace Ja\Shipping\Services;

use EasyPost\EasyPostClient;
use EasyPost\Exception\General\EasyPostException;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class EasyPost
{
    protected $client;

    const testTrackingNumbers = [
        'EZ1000000001',
        'EZ2000000002',
        'EZ7000000007',
    ];

    public function __construct()
    {
        $apiKey = env('EASYPOST_API_KEY');
        $this->client = new EasyPostClient($apiKey);
    }

    public function addressCreate(array $address): mixed
    {
        return $this->client->address->create($address);
    }

    public function rateRetrieve(string $shippingRateId): mixed
    {
        return $this->client->rate->retrieve($shippingRateId);
    }

    public function orderBuy(string $orderId, string $rateId): mixed
    {
        $rate = $this->rateRetrieve($rateId);

        return $this->client->order->buy($orderId, $rate);
    }

    public function shipmentBuy(string $shipmentId, string $shippingRateId, ?bool $insurance = null): mixed
    {
        $rate = $this->rateRetrieve($shippingRateId);

        return $this->client->shipment->buy($shipmentId, [
            'rate' => $rate,
            'insurance' => $insurance,
        ]);
    }

    public function trackerCreate(string $trackingCode, string $carrier): mixed
    {
        return $this->client->tracker->create([
            'tracking_code' => $trackingCode,
            'carrier' => $carrier,
        ]);
    }

    public function orderOrShipmentCreate(array $fromAddress, array $toAddress, array $parcels): mixed
    {
        if (count($parcels) === 1) {
            return $this->client->shipment->create([
                'from_address' => $fromAddress,
                'to_address' => $toAddress,
                'parcel' => $parcels[0],
            ]);
        }

        try {
            $order = $this->client->order->create([
                'from_address' => $fromAddress,
                'to_address' => $toAddress,
                'shipments' => collect($parcels)->map(fn ($parcel) => ['parcel' => $parcel])->toArray(),
            ]);
        } catch (EasyPostException $e) {
            $errors = collect($e->errors);

            if ($errors->count() > 0) {
                throw new Exception($errors->first()['field'].': '.$errors->first()['message']);
            }

            throw $e;
        }

        return $order;
    }
}
