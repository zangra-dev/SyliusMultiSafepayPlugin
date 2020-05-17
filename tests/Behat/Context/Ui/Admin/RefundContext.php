<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusMultiSafepayPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Context\Ui\Admin\ManagingOrdersContext;
use Sylius\Component\Core\Model\OrderInterface;
use Tests\BitBag\SyliusMultiSafepayPlugin\Behat\Mocker\MultiSafepayApiClientMocker;

final class RefundContext implements Context
{
    /** @var MultiSafepayApiClientMocker */
    private $multiSafepayApiClientMocker;

    /** @var ManagingOrdersContext */
    private $managingOrdersContext;

    public function __construct(
        MultiSafepayApiClientMocker $multiSafepayApiClientMocker,
        ManagingOrdersContext $managingOrdersContext
    ) {
        $this->multiSafepayApiClientMocker = $multiSafepayApiClientMocker;
        $this->managingOrdersContext = $managingOrdersContext;
    }

    /**
     * @When /^I mark (this order)'s multisafepay payment as refunded$/
     */
    public function iMarkThisOrdersMultisafepayPaymentAsRefunded(OrderInterface $order): void
    {
        $this->multiSafepayApiClientMocker->mockApiRefundedPayment(function () use ($order) {
            $this->managingOrdersContext->iMarkThisOrderSPaymentAsRefunded($order);
        });
    }
}
