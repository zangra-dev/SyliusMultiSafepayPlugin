<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\SyliusMultiSafepayPlugin\Action;

use BitBag\SyliusMultiSafepayPlugin\ApiClient\MultiSafepayApiClientInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\GetStatusInterface;
use Sylius\Component\Core\Model\PaymentInterface;

final class StatusAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getModel();

        $details = $payment->getDetails();

        if (!isset($details['status']) || !isset($details['orderId'])) {
            $request->markNew();

            return;
        }

        $this->gateway->execute($httpRequest = new GetHttpRequest());

        if (isset($httpRequest->query['type']) && MultiSafepayApiClientInterface::STATUS_CANCEL === $httpRequest->query['status']) {
            $details['status'] = MultiSafepayApiClientInterface::STATUS_CANCELED;

            $payment->setDetails($details);
        }

        switch ($details['status']) {
            case MultiSafepayApiClientInterface::STATUS_CANCELED:
                $request->markCanceled();

                break;
            case MultiSafepayApiClientInterface::STATUS_COMPLETED:
                $request->markCaptured();

                break;
            case MultiSafepayApiClientInterface::STATUS_INITIALIZED:
            case MultiSafepayApiClientInterface::STATUS_UNCLEARED:
                $request->markPending();

                break;
            case MultiSafepayApiClientInterface::STATUS_DECLINED:
                $request->markFailed();

                break;
        }
    }

    public function supports($request): bool
    {
        return
            $request instanceof GetStatusInterface &&
            $request->getModel() instanceof PaymentInterface
        ;
    }
}
