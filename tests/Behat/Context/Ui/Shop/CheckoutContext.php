<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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
