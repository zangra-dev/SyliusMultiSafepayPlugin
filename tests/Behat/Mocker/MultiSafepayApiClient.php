<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusMultiSafepayPlugin\Behat\Mocker;

use BitBag\SyliusMultiSafepayPlugin\ApiClient\MultiSafepayApiClientInterface;
use MultiSafepayAPI\Object\Orders;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MultiSafepayApiClient implements MultiSafepayApiClientInterface
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getOrderById(string $id): \stdClass
    {
        return $this->container->get('bitbag_sylius_multisafepay_plugin.api_client.multisafepay_api_client')->getOrderById($id);
    }

    public function getType(): string
    {
        return $this->container->get('bitbag_sylius_multisafepay_plugin.api_client.multisafepay_api_client')->getType();
    }

    public function getAllowMultiCurrency(): bool
    {
        return $this->container->get('bitbag_sylius_multisafepay_plugin.api_client.multisafepay_api_client')->getAllowMultiCurrency();
    }

    public function initialise(string $apiKey, string $type, bool $sandbox = true,  bool $allowMultiCurrency = false): void
    {
        $this->container->get('bitbag_sylius_multisafepay_plugin.api_client.multisafepay_api_client')->initialise(
            $apiKey,
            $type,
            $sandbox,
            $allowMultiCurrency
        );
    }

    public function createPayment(array $data): Orders
    {
        return $this->container->get('bitbag_sylius_multisafepay_plugin.api_client.multisafepay_api_client')->createPayment($data);
    }

    public function refund(string $orderId, int $amount, string $currencyCode): void
    {
        $this->container->get('bitbag_sylius_multisafepay_plugin.api_client.multisafepay_api_client')->refund(
            $orderId,
            $amount,
            $currencyCode
        );
    }

    public function isPaymentActive(string $status): bool
    {
        return $this->container->get('bitbag_sylius_multisafepay_plugin.api_client.multisafepay_api_client')->isPaymentActive($status);
    }
}
