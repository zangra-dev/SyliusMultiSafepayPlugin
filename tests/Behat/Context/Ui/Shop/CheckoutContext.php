<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusMultiSafepayPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use BitBag\SyliusMultiSafepayPlugin\ApiClient\MultiSafepayApiClientInterface;
use Sylius\Behat\Page\Shop\Checkout\CompletePageInterface;
use Sylius\Behat\Page\Shop\Order\ShowPageInterface;
use Tests\BitBag\SyliusMultiSafepayPlugin\Behat\Mocker\MultiSafepayApiClientMocker;
use Tests\BitBag\SyliusMultiSafepayPlugin\Behat\Page\Shop\Payum\PaymentPageInterface;

final class CheckoutContext implements Context
{
    /** @var CompletePageInterface */
    private $summaryPage;

    /** @var ShowPageInterface */
    private $orderDetails;

    /** @var MultiSafepayApiClientMocker */
    private $multiSafepayApiClientMocker;

    /** @var PaymentPageInterface */
    private $paymentPage;

    public function __construct(
        CompletePageInterface $summaryPage,
        ShowPageInterface $orderDetails,
        MultiSafepayApiClientMocker $multiSafepayApiClientMocker,
        PaymentPageInterface $paymentPage
    ) {
        $this->summaryPage = $summaryPage;
        $this->orderDetails = $orderDetails;
        $this->multiSafepayApiClientMocker = $multiSafepayApiClientMocker;
        $this->paymentPage = $paymentPage;
    }

    /**
     * @When I confirm my order with MultiSafepay payment
     * @Given I have confirmed my order with MultiSafepay payment
     */
    public function iConfirmMyOrderWithMultisafepayPayment(): void
    {
        $this->multiSafepayApiClientMocker->mockApiCreatePayment(function () {
            $this->summaryPage->confirmOrder();
        });
    }

    /**
     * @When I sign in to MultiSafepay and pay successfully
     */
    public function iSignInToMultisafepayAndPaySuccessfully(): void
    {
        $this->multiSafepayApiClientMocker->mockApiSuccessfulPayment(function () {
            $this->paymentPage->notify(['transactionid' => 'test']);
            $this->paymentPage->capture();
        });
    }

    /**
     * @When I cancel my MultiSafepay payment
     * @Given I have cancelled MultiSafepay payment
     */
    public function iCancelMyMultisafepayPayment(): void
    {
        $this->multiSafepayApiClientMocker->mockApiCancelledPayment(function () {
            $this->paymentPage->capture(['type' => MultiSafepayApiClientInterface::STATUS_CANCEL]);
        });
    }

    /**
     * @When I try to pay again MultiSafepay payment
     */
    public function iTryToPayAgainMultisafepayPayment(): void
    {
        $this->multiSafepayApiClientMocker->mockApiCreatePayment(function () {
            $this->orderDetails->pay();
        });
    }

    /**
     * @Given I have failed MultiSafepay payment
     */
    public function iHaveFailedMultisafepayPayment(): void
    {
        $this->multiSafepayApiClientMocker->mockApiFailedPayment(function () {
            $this->paymentPage->notify(['transactionid' => 'test']);
            $this->paymentPage->capture();
        });
    }
}
