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

use BitBag\SyliusMultiSafepayPlugin\Action\Api\ApiAwareTrait;
use BitBag\SyliusMultiSafepayPlugin\MultiSafepayGatewayFactory;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\Notify;
use Psr\Log\LoggerInterface;
use SM\Factory\FactoryInterface;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\PaymentTransitions;

final class NotifyAction implements ActionInterface, ApiAwareInterface, GatewayAwareInterface
{
    use GatewayAwareTrait, ApiAwareTrait;

    /** @var LoggerInterface */
    private $logger;

    /** @var FactoryInterface */
    private $stateMachineFactory;

    public function __construct(LoggerInterface $logger, FactoryInterface $stateMachineFactory)
    {
        $this->logger = $logger;
        $this->stateMachineFactory = $stateMachineFactory;
    }

    public function execute($request): void
    {
        /** @var Notify $request  */
        $details = ArrayObject::ensureArrayObject($request->getModel());

        $this->gateway->execute($httpRequest = new GetHttpRequest());

        if (!isset($httpRequest->query['transactionid'])) {
            throw new HttpResponse(null, 400);
        }

        /** @var PaymentInterface $payment */
        $payment = $request->getFirstModel();

        /** @var OrderInterface $order */
        $order = $payment->getOrder();

        /** @var PaymentInterface $item */
        foreach ($order->getPayments() as $item) {
            /** @var PaymentMethodInterface $method */
            $method = $item->getMethod();

            /** @var GatewayConfigInterface $gatewayConfig */
            $gatewayConfig = $method->getGatewayConfig();

            if (
                PaymentInterface::STATE_NEW === $item->getState() &&
                MultiSafepayGatewayFactory::FACTORY_NAME === $gatewayConfig->getFactoryName() &&
                $payment !== $item
            ) {
                $order->removePayment($item);
            }
        }

        $paymentStateMachine = $this->stateMachineFactory->get($payment, PaymentTransitions::GRAPH);

        $orderData = $this->multiSafepayApiClient->getOrderById($details['orderId']);

        if (
            (PaymentInterface::STATE_FAILED === $payment->getState() || PaymentInterface::STATE_CANCELLED === $payment->getState()) &&
            $paymentStateMachine->can(PaymentTransitions::TRANSITION_PROCESS) &&
            $this->multiSafepayApiClient->isPaymentActive($orderData->status)
        ) {
            $paymentStateMachine->apply(PaymentTransitions::TRANSITION_PROCESS);
        }

        $details['status'] = $orderData->status;

        throw new HttpResponse('OK', 200);
    }

    public function supports($request): bool
    {
        return
            $request instanceof Notify &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
