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
use Sylius\Behat\Service\Mocker\MockerInterface;

final class MultiSafepayApiClientMocker
{
    /** @var MockerInterface */
    private $mocker;

    public function __construct(MockerInterface $mocker)
    {
        $this->mocker = $mocker;
    }

    public function mockApiCreatePayment(callable $action): void
    {
        $mock = $this->mocker->mockService(
            'bitbag_sylius_multisafepay_plugin.api_client.multisafepay_api_client',
            MultiSafepayApiClientInterface::class
        );

        $mock
            ->shouldReceive('initialise')
        ;

        $mock
            ->shouldReceive('getAllowMultiCurrency')
        ;

        $orders = \Mockery::mock('orders', Orders::class);

        $orders
            ->shouldNotReceive('getPaymentLink')
            ->andReturn('url')
        ;

        $orders->data = (object) [
            'order_id' => 'test',
        ];

        $mock
            ->shouldReceive('createPayment')
            ->andReturn($orders)
        ;

        $mock
            ->shouldReceive('getType')
            ->andReturn(MultiSafepayApiClientInterface::REDIRECT_ORDER_TYPE)
        ;

        $action();

        $this->mocker->unmockAll();
    }

    public function mockApiSuccessfulPayment(callable $action): void
    {
        $mock = $this->mocker->mockService(
            'bitbag_sylius_multisafepay_plugin.api_client.multisafepay_api_client',
            MultiSafepayApiClientInterface::class
        );

        $mock
            ->shouldReceive('initialise')
        ;

        $mock
            ->shouldNotReceive('getOrderById')
            ->andReturn((object) [
                'status' => MultiSafepayApiClientInterface::STATUS_COMPLETED,
            ])
        ;

        $action();

        $this->mocker->unmockAll();
    }

    public function mockApiFailedPayment(callable $action): void
    {
        $mock = $this->mocker->mockService(
            'bitbag_sylius_multisafepay_plugin.api_client.multisafepay_api_client',
            MultiSafepayApiClientInterface::class
        );

        $mock
            ->shouldReceive('initialise')
        ;

        $mock
            ->shouldNotReceive('getOrderById')
            ->andReturn((object) [
                'status' => MultiSafepayApiClientInterface::STATUS_DECLINED,
            ])
        ;

        $action();

        $this->mocker->unmockAll();
    }

    public function mockApiCancelledPayment(callable $action): void
    {
        $mock = $this->mocker->mockService(
            'bitbag_sylius_multisafepay_plugin.api_client.multisafepay_api_client',
            MultiSafepayApiClientInterface::class
        );

        $mock
            ->shouldReceive('initialise')
        ;

        $mock
            ->shouldReceive('getAllowMultiCurrency')
        ;

        $action();

        $this->mocker->unmockAll();
    }

    public function mockApiRefundedPayment(callable $action): void
    {
        $mock = $this->mocker->mockService(
            'bitbag_sylius_multisafepay_plugin.api_client.multisafepay_api_client',
            MultiSafepayApiClientInterface::class
        );

        $mock
            ->shouldReceive('initialise')
        ;

        $mock
            ->shouldReceive('refund')
        ;

        $action();

        $this->mocker->unmockAll();
    }
}
