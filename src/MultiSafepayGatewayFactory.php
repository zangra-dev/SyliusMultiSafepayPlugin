<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusMultiSafepayPlugin;

use BitBag\SyliusMultiSafepayPlugin\ApiClient\MultiSafepayApiClientInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

final class MultiSafepayGatewayFactory extends GatewayFactory
{
    public const FACTORY_NAME = 'multisafepay';

    protected function populateConfig(ArrayObject $config): void
    {
        $config->defaults([
            'payum.factory_name' => self::FACTORY_NAME,
            'payum.factory_title' => 'MultiSafepay',
            'payum.http_client' => '@bitbag_sylius_multisafepay_plugin.api_client.multisafepay_api_client',
        ]);

        if (false === (bool) $config['payum.api']) {
            $config['payum.default_options'] = [
                'apiKey' => null,
                'sandbox' => true,
                'type' => null,
                'allow_multi_currency' => false,
            ];

            $config->defaults($config['payum.default_options']);

            $config['payum.required_options'] = [
                'apiKey',
                'type',
            ];

            $config['payum.api'] = function (ArrayObject $config): MultiSafepayApiClientInterface {
                $config->validateNotEmpty($config['payum.required_options']);

                /** @var MultiSafepayApiClientInterface $multiSafepayApiClient */
                $multiSafepayApiClient = $config['payum.http_client'];
                $multiSafepayApiClient->initialise($config['apiKey'], $config['type'], $config['sandbox'], $config['allow_multi_currency']);

                return $multiSafepayApiClient;
            };
        }
    }
}
