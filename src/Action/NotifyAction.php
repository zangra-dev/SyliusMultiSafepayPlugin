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
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\Notify;
use Payum\Core\Bridge\Spl\ArrayObject;
use Psr\Log\LoggerInterface;

final class NotifyAction implements ActionInterface, ApiAwareInterface, GatewayAwareInterface
{
    use GatewayAwareTrait, ApiAwareTrait;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function execute($request): void
    {
        $details = ArrayObject::ensureArrayObject($request->getModel());

        $this->gateway->execute($httpRequest = new GetHttpRequest());

        if (!isset($httpRequest->query['transactionid'])) {
            throw new HttpResponse(null, 400);
        }

        $order = $this->multiSafepayApiClient->getOrderById($details['orderId']);

        $details['status'] = $order->status;

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
