<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusMultiSafepayPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use BitBag\SyliusMultiSafepayPlugin\MultiSafepayGatewayFactory;
use Doctrine\ORM\EntityManagerInterface;
use Payum\Core\Registry\RegistryInterface;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\PaymentTransitions;

final class OrderContext implements Context
{
    /** @var EntityManagerInterface */
    private $objectManager;

    /** @var StateMachineFactoryInterface */
    private $stateMachineFactory;

    /** @var RegistryInterface */
    private $payum;

    public function __construct(
        EntityManagerInterface $objectManager,
        StateMachineFactoryInterface $stateMachineFactory,
        RegistryInterface $payum
    ) {
        $this->objectManager = $objectManager;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->payum = $payum;
    }

    /**
     * @Given /^(this order) with multisafepay payment is already paid$/
     */
    public function thisOrderWithMultisafepayPaymentIsAlreadyPaid(OrderInterface $order): void
    {
        $this->applyMultiSafepayPaymentTransitionOnOrder($order, PaymentTransitions::TRANSITION_COMPLETE);

        $this->objectManager->flush();
    }

    private function applyMultiSafepayPaymentTransitionOnOrder(OrderInterface $order, $transition): void
    {
        foreach ($order->getPayments() as $payment) {
            /** @var PaymentMethodInterface $paymentMethod */
            $paymentMethod = $payment->getMethod();

            if (MultiSafepayGatewayFactory::FACTORY_NAME === $paymentMethod->getGatewayConfig()->getFactoryName()) {
                $model['orderId'] = 'test';

                $payment->setDetails($model);
            }

            $this->stateMachineFactory->get($payment, PaymentTransitions::GRAPH)->apply($transition);
        }
    }
}
