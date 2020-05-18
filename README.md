<h1 align="center">
    <a href="https://packagist.org/packages/bitbag/multisafepay-plugin" title="License" target="_blank">
        <img src="https://img.shields.io/packagist/l/bitbag/multisafepay-plugin.svg" />
    </a>
    <a href="https://packagist.org/packages/bitbag/multisafepay-plugin" title="Version" target="_blank">
        <img src="https://img.shields.io/packagist/v/bitbag/multisafepay-plugin.svg" />
    </a>
    <a href="http://travis-ci.org/BitBagCommerce/SyliusMultiSafepayPlugin" title="Build status" target="_blank">
        <img src="https://img.shields.io/travis/BitBagCommerce/SyliusMultiSafepayPlugin/master.svg" />
    </a>
    <a href="https://scrutinizer-ci.com/g/BitBagCommerce/SyliusMultiSafepayPlugin/" title="Scrutinizer" target="_blank">
        <img src="https://img.shields.io/scrutinizer/g/BitBagCommerce/SyliusMultiSafepayPlugin.svg" />
    </a>
    <a href="https://packagist.org/packages/bitbag/multisafepay-plugin" title="Total Downloads" target="_blank">
        <img src="https://poser.pugx.org/bitbag/multisafepay-plugin/downloads" />
    </a>
    <p>
        <img src="https://sylius.com/assets/badge-approved-by-sylius.png" width="85">
    </p>
</h1>

## About us

At BitBag we do believe in open source. However, we are able to do it just because of our awesome clients, who are kind enough to share some parts of our work with the community. Therefore, if you feel like there is a possibility for us working together, feel free to reach us out. You will find out more about our professional services, technologies and contact details at https://bitbag.io.

## Overview

This plugin allows you to integrate MultiSafepay payment with Sylius platform app.

## Installation

1. Require plugin with composer:

    ```bash
    composer require bitbag/multisafepay-plugin
    ```

2. Import configuration in your `config/packages/bitbag_sylius_multisafepay_plugin.yaml` file:

    ```yaml
    imports:
        - { resource: "@BitBagSyliusMultiSafepayPlugin/Resources/config/config.yml" }
    ```

3. Add plugin class to your `config/bundles.php` file:

    ```php
    $bundles = [
        BitBag\SyliusMultiSafepayPlugin\BitBagSyliusMultiSafepayPlugin::class => ['all' => true],
    ];
    ```

4. Clear cache:

    ```bash
    bin/console cache:clear
    ```
 
## Customization

### Available services you can [decorate](https://symfony.com/doc/current/service_container/service_decoration.html) and forms you can [extend](http://symfony.com/doc/current/form/create_form_type_extension.html)

Run the below command to see what Symfony services are shared with this plugin:
 
```bash
$ bin/console debug:container bitbag_sylius_multisafepay_plugin
```

## Testing

```bash
$ composer install
$ cd tests/Application
$ yarn install
$ yarn run gulp
$ bin/console assets:install web -e test
$ bin/console doctrine:database:create -e test
$ bin/console doctrine:schema:create -e test
$ bin/console server:run 127.0.0.1:8080 -d web -e test
$ open http://localhost:8080
$ bin/behat
$ bin/phpspec run
```

## Contribution

Learn more about our contribution workflow on http://docs.sylius.org/en/latest/contributing/.
