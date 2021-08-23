<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusMultiSafepayPlugin\PaymentProcessing;

use BitBag\SyliusMultiSafepayPlugin\ApiClient\MultiSafepayApiClientInterface;
use BitBag\SyliusMultiSafepayPlugin\MultiSafepayGatewayFactory;
use Psr\Log\LoggerInterface;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Exception\UpdateHandlingException;
use Symfony\Component\HttpFoundation\Session\Session;

final class RefundPaymentProcessor implements PaymentProcessorInterface
{
    /** @var Session */
    private $session;

    /** @var MultiSafepayApiClientInterface */
    private $multiSafepayApiClient;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        Session $session,
        MultiSafepayApiClientInterface $multiSafepayApiClient,
        LoggerInterface $logger
    ) {
        $this->session = $session;
        $this->multiSafepayApiClient = $multiSafepayApiClient;
        $this->logger = $logger;
    }

    public function process(PaymentInterface $payment): void
    {
        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $payment->getMethod();

        $details = $payment->getDetails();

        /** @var GatewayConfigInterface $gatewayConfig */
        $gatewayConfig = $paymentMethod->getGatewayConfig();

        if (
            MultiSafepayGatewayFactory::FACTORY_NAME !== $gatewayConfig->getFactoryName() ||
            (isset($details['status']) && MultiSafepayApiClientInterface::STATUS_REFUNDED === $details['status'])
        ) {
            return;
        }

        if (!isset($details['orderId'])) {
            $this->session->getFlashBag()->add('info', 'The payment refund was made only locally.');

            return;
        }

        $gatewayConfig = $gatewayConfig->getConfig();

        $this->multiSafepayApiClient->initialise(
            $gatewayConfig['apiKey'],
            $gatewayConfig['type'],
            $gatewayConfig['sandbox']
        );

        try {
            $this->multiSafepayApiClient->refund($details['orderId'], (int) $payment->getAmount(), (string) $payment->getCurrencyCode());
        } catch (\Exception $exception) {
            $message = $exception->getMessage();

            $this->session->getFlashBag()->add('error', htmlspecialchars($message));

            throw new UpdateHandlingException();
        }
    }
}
