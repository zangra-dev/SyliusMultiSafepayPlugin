<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\SyliusMultiSafepayPlugin\Action\Api;

use BitBag\SyliusMultiSafepayPlugin\ApiClient\MultiSafepayApiClient;
use Payum\Core\Exception\UnsupportedApiException;

trait ApiAwareTrait
{
    /** @var MultiSafepayApiClient */
    protected $multiSafepayApiClient;

    public function setApi($multiSafepayApiClient): void
    {
        if (!$multiSafepayApiClient instanceof MultiSafepayApiClient) {
            throw new UnsupportedApiException('Not supported.Expected an instance of ' . MultiSafepayApiClient::class);
        }

        $this->multiSafepayApiClient = $multiSafepayApiClient;
    }
}
