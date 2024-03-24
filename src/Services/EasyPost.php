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

    public function rateRetrieve(string $rateId): mixed
    {
        return $this->client->rate->retrieve($rateId);
    }

    // public function batchShipmentCreate(array $data)
    // {
    //     $batch = $this->client->batch->create([
    //         'reference' => 'batch_shipment',
    //     ]);

    //     return $batch;
    // }

    public function orderBuy(string $orderId, string $rateId): mixed
    {
        $rate = $this->rateRetrieve($rateId);

        return $this->client->order->buy($orderId, $rate);
    }

    public function shipmentBuy(string $shipmentId, string $rateId, ?bool $insurance = null): mixed
    {
        $rate = $this->rateRetrieve($rateId);

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

    public function shipmentRetrieve(string $shipmentId): mixed
    {
        return $this->client->shipment->retrieve($shipmentId);
    }

    public function shipmentCreate(
        array $fromAddress,
        array $toAddress,
        array $parcel,
        ?array $carrierAccounts = null,
        ?array $options = null
    ): mixed {
        return $this->client->shipment->create(array_merge(
                [
                    'from_address' => $fromAddress,
                    'to_address' => $toAddress,
                    'parcel' => $parcel,
                ],
                $carrierAccounts == null ? [] : [
                    'carrier_accounts' => $carrierAccounts
                ],
                $options == null ? [] : [
                    'options' => $options
                ],
        ));
    }
}
