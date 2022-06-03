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
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\RuntimeException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Request\Capture;
use Payum\Core\Security\GenericTokenFactoryAwareInterface;
use Payum\Core\Security\GenericTokenFactoryInterface;
use Payum\Core\Security\TokenInterface;
use Psr\Log\LoggerInterface;

final class CaptureAction implements ActionInterface, ApiAwareInterface, GatewayAwareInterface, GenericTokenFactoryAwareInterface
{
    use GatewayAwareTrait, ApiAwareTrait;

    /** @var GenericTokenFactoryInterface|null */
    private $tokenFactory;

    /** @var LoggerInterface */
    private $logger;

    /** @var string|null */
    private $notificationUrlHostname;

    public function __construct(LoggerInterface $logger, string $notificationUrlHostname = null)
    {
        $this->logger = $logger;
        $this->notificationUrlHostname = $notificationUrlHostname;
    }

    public function setGenericTokenFactory(GenericTokenFactoryInterface $genericTokenFactory = null): void
    {
        $this->tokenFactory = $genericTokenFactory;
    }

    public function execute($request): void
    {
        /** @param Capture $request */
        RequestNotSupportedException::assertSupports($this, $request);

        $details = ArrayObject::ensureArrayObject($request->getModel());

        if (isset($details['orderId'])) {
            return;
        }

        /** @var TokenInterface $token */
        $token = $request->getToken();

        if (null === $this->tokenFactory) {
            throw new RuntimeException();
        }

        $notifyToken = $this->tokenFactory->createNotifyToken($token->getGatewayName(), $token->getDetails());

        $notificationUrl = $notifyToken->getTargetUrl();

        if (!empty($this->notificationUrlHostname)) {
            $parseNotificationUrl = parse_url($notificationUrl);

            $notificationUrl = sprintf('%s%s', $this->notificationUrlHostname, $parseNotificationUrl['path']);
        }

        $paymentData = $details['paymentData'];

        $paymentData['payment_options'] = [
            'notification_url' => $notificationUrl,
            'redirect_url' => $token->getTargetUrl(),
            'cancel_url' => sprintf('%s?type=%s', $token->getTargetUrl(), MultiSafepayApiClientInterface::STATUS_CANCEL),
        ];

        $details['paymentData'] = $paymentData;

        $order = $this->multiSafepayApiClient->createPayment($paymentData);

        $details['status'] = MultiSafepayApiClientInterface::STATUS_INITIALIZED;
        $details['paymentLink'] = $order->getPaymentLink();
        $details['orderId'] = $order->data->order_id;

        throw new HttpRedirect($order->getPaymentLink());
    }

    public function supports($request): bool
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
