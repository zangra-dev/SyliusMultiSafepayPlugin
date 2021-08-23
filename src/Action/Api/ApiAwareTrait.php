<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusMultiSafepayPlugin\Action\Api;

use BitBag\SyliusMultiSafepayPlugin\ApiClient\MultiSafepayApiClientInterface;
use Payum\Core\Exception\UnsupportedApiException;

trait ApiAwareTrait
{
    /** @var MultiSafepayApiClientInterface */
    protected $multiSafepayApiClient;

    public function setApi($multiSafepayApiClient): void
    {
        if (!$multiSafepayApiClient instanceof MultiSafepayApiClientInterface) {
            throw new UnsupportedApiException('Not supported.Expected an instance of ' . MultiSafepayApiClientInterface::class);
        }

        $this->multiSafepayApiClient = $multiSafepayApiClient;
    }
}
