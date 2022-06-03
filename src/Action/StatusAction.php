<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusMultiSafepayPlugin\Action;

use BitBag\SyliusMultiSafepayPlugin\Action\Api\ApiAwareTrait;
use BitBag\SyliusMultiSafepayPlugin\ApiClient\MultiSafepayApiClientInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\GetStatusInterface;
use Sylius\Component\Core\Model\PaymentInterface;

final class StatusAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
{
    use GatewayAwareTrait;
    use ApiAwareTrait;

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

        if (isset($httpRequest->query['type']) && MultiSafepayApiClientInterface::STATUS_CANCEL === $httpRequest->query['type']) {
            $details['status'] = MultiSafepayApiClientInterface::STATUS_CANCELED;
        } elseif ($details['status'] !== MultiSafepayApiClientInterface::STATUS_CANCELED) {
            $order = $this->multiSafepayApiClient->getOrderById($details['orderId']);

            $details['status'] = $order->status;
        }

        $payment->setDetails($details);

        switch ($details['status']) {
            case MultiSafepayApiClientInterface::STATUS_CANCELED:
            case MultiSafepayApiClientInterface::STATUS_VOID:
                $request->markCanceled();

                break;
            case MultiSafepayApiClientInterface::STATUS_COMPLETED:
                $request->markCaptured();

                break;
            case MultiSafepayApiClientInterface::STATUS_INITIALIZED:
            case MultiSafepayApiClientInterface::STATUS_UNCLEARED:
            case MultiSafepayApiClientInterface::STATUS_RESERVED:
                $request->markPending();

                break;
            case MultiSafepayApiClientInterface::STATUS_DECLINED:
            case MultiSafepayApiClientInterface::STATUS_EXPIRED:
                $request->markFailed();

                break;
            case MultiSafepayApiClientInterface::STATUS_REFUNDED:
                $request->markRefunded();

                break;
            default:
                $request->markUnknown();

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
