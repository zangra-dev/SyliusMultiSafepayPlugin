<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusMultiSafepayPlugin\Action;

use BitBag\SyliusMultiSafepayPlugin\Action\CaptureAction;
use BitBag\SyliusMultiSafepayPlugin\ApiClient\MultiSafepayApiClientInterface;
use MultiSafepayAPI\Object\Orders;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayInterface;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Request\Capture;
use Payum\Core\Security\GenericTokenFactory;
use Payum\Core\Security\TokenInterface;
use PhpSpec\ObjectBehavior;
use Psr\Log\LoggerInterface;

final class CaptureActionSpec extends ObjectBehavior
{
    function let(LoggerInterface $logger): void
    {
        $this->beConstructedWith($logger);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(CaptureAction::class);
    }

    function it_implements_action_interface(): void
    {
        $this->shouldHaveType(ActionInterface::class);
    }

    function it_implements_api_aware_interface(): void
    {
        $this->shouldHaveType(ApiAwareInterface::class);
    }

    function it_implements_gateway_aware_interface(): void
    {
        $this->shouldHaveType(GatewayAwareInterface::class);
    }

    function it_executes(
        Capture $request,
        ArrayObject $arrayObject,
        TokenInterface $token,
        GatewayInterface $gateway,
        MultiSafepayApiClientInterface $multiSafepayApiClient,
        GenericTokenFactory $genericTokenFactory,
        TokenInterface $notifyToken,
        Orders $orders
    ): void {
        $orders->getPaymentLink()->willReturn('redirect_url');

        $orders->data = (object) [
            'order_id' => 'test',
        ];

        $this->setGateway($gateway);
        $this->setApi($multiSafepayApiClient);
        $this->setGenericTokenFactory($genericTokenFactory);
        $arrayObject->getArrayCopy()->willReturn([]);
        $request->getModel()->willReturn($arrayObject);
        $request->getToken()->willReturn($token);
        $token->getGatewayName()->willReturn('test');
        $token->getDetails()->willReturn([]);
        $multiSafepayApiClient->createPayment([
            'payment_options' => [
                'notification_url' => 'url',
                'redirect_url' => 'url',
                'cancel_url' => sprintf('%s?type=%s', 'url', MultiSafepayApiClientInterface::STATUS_CANCEL),
            ],
        ])->willReturn($orders);
        $token->getTargetUrl()->willReturn('url');
        $notifyToken->getTargetUrl()->willReturn('url');
        $notifyToken->getHash()->willReturn('test');
        $genericTokenFactory->createNotifyToken('test', [])->willReturn($notifyToken);

        $arrayObject->offsetExists('orderId')->shouldBeCalled();
        $arrayObject->offsetGet('paymentData')->shouldBeCalled();
        $arrayObject->offsetSet('status', 'initialized')->shouldBeCalled();
        $arrayObject->offsetSet('paymentLink', 'redirect_url')->shouldBeCalled();
        $arrayObject->offsetSet('orderId', 'test')->shouldBeCalled();
        $arrayObject->offsetSet('paymentData', [
            'payment_options' => [
                'notification_url' => 'url',
                'redirect_url' => 'url',
                'cancel_url' => 'url?type=cancel',
            ],
        ])->shouldBeCalled();

        $this
            ->shouldThrow(HttpRedirect::class)
            ->during('execute', [$request])
        ;
    }

    function it_supports_only_capture_request_and_array_access(
        Capture $request,
        \ArrayAccess $arrayAccess
    ): void {
        $request->getModel()->willReturn($arrayAccess);
        $this->supports($request)->shouldReturn(true);
    }
}
