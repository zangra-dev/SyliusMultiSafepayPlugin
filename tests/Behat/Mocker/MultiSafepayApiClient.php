<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
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

    public function initialise(string $apiKey, string $type, bool $sandbox = true): void
    {
        $this->container->get('bitbag_sylius_multisafepay_plugin.api_client.multisafepay_api_client')->initialise(
            $apiKey,
            $type,
            $sandbox
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
