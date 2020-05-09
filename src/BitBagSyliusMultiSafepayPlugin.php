<?php

declare(strict_types=1);

namespace BitBag\SyliusMultiSafepayPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class BitBagSyliusMultiSafepayPlugin extends Bundle
{
    use SyliusPluginTrait;
}
