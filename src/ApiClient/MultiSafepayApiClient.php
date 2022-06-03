<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusMultiSafepayPlugin\ApiClient;

use MultiSafepayAPI\Client;
use MultiSafepayAPI\Object\Orders;

class MultiSafepayApiClient implements MultiSafepayApiClientInterface
{
    /** @var Client */
    private $client;

    /** @var string */
    private $type;

    /** @var bool */
    private $allowMultiCurrency;

    public function initialise(string $apiKey, string $type, bool $sandbox = true, bool $allowMultiCurrency = false): void
    {
        $this->type = $type;
        $this->allowMultiCurrency = $allowMultiCurrency;

        $this->client = new Client();
        $this->client->setApiKey($apiKey);
        $this->client->setApiUrl(
            $sandbox ? self::API_URL_TEST : self::API_URL_LIVE
        );
    }

    public function createPayment(array $data): Orders
    {
        $this->client->orders->post($data);

        return $this->client->orders;
    }

    public function getOrderById(string $id): \stdClass
    {
        return $this->client->orders->get('orders', $id);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAllowMultiCurrency(): bool
    {
        return $this->allowMultiCurrency;
    }

    public function refund(string $orderId, int $amount, string $currencyCode): void
    {
        $endpoint = sprintf('orders/%s/refunds', $orderId);

        $this->client->orders->post([
            'type' => 'refund',
            'amount' => $amount,
            'currency' => $currencyCode,
        ], $endpoint);
    }

    public function isPaymentActive(string $status): bool
    {
        return in_array($status, [self::STATUS_INITIALIZED, self::STATUS_COMPLETED, self::STATUS_UNCLEARED, self::STATUS_RESERVED], true);
    }
}
