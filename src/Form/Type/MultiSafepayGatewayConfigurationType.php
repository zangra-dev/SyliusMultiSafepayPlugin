<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusMultiSafepayPlugin\Form\Type;

use BitBag\SyliusMultiSafepayPlugin\ApiClient\MultiSafepayApiClientInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;

final class MultiSafepayGatewayConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('apiKey', TextType::class, [
                'label' => 'bitbag_sylius_multisafepay.ui.api_key',
                'constraints' => [
                    new NotBlank([
                        'message' => 'bitbag_sylius_multisafepay.api_key.not_blank',
                        'groups' => 'sylius',
                    ]),
                ],
            ])
            ->add('sandbox', CheckboxType::class, [
                'label' => 'bitbag_sylius_multisafepay.ui.sandbox',
            ])
            ->add('allow_multi_currency', CheckboxType::class, [
                'label' => 'bitbag_sylius_multisafepay.ui.allow_multi_currency',
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'bitbag_sylius_multisafepay.ui.type',
                'choices' => [
                    'bitbag_sylius_multisafepay.ui.payment_link' => MultiSafepayApiClientInterface::PAYMENT_LINK_TYPE,
                    'bitbag_sylius_multisafepay.ui.redirect_order' => MultiSafepayApiClientInterface::REDIRECT_ORDER_TYPE,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'bitbag_sylius_multisafepay.type.not_blank',
                        'groups' => 'sylius',
                    ]),
                ],
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
                $data = $event->getData();

                $data['payum.http_client'] = '@bitbag_sylius_multisafepay_plugin.api_client.multisafepay_api_client';

                $event->setData($data);
            })
        ;
    }
}
