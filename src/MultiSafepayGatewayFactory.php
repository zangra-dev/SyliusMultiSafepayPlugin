<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\SyliusMultiSafepayPlugin;

use BitBag\SyliusMultiSafepayPlugin\ApiClient\MultiSafepayApiClientInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

final class MultiSafepayGatewayFactory extends GatewayFactory
{
    public const FACTORY_NAME = 'multisafepay';

    protected function populateConfig(ArrayObject $config)
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
            ];

            $config->defaults($config['payum.default_options']);

            $config['payum.required_options'] = [
                'apiKey',
                'type',
            ];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                /** @var MultiSafepayApiClientInterface $multiSafepayApiClient */
                $multiSafepayApiClient = $config['payum.http_client'];

                $multiSafepayApiClient->initialise($config['apiKey'], $config['type'], $config['sandbox']);

                return $multiSafepayApiClient;
            };
        }
    }
}
