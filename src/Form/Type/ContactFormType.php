<?php

declare(strict_types=1);

namespace MangoSylius\SyliusContactFormPlugin\Form\Type;

use MangoSylius\SyliusContactFormPlugin\Service\ContactFormSettingsProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ContactFormType extends AbstractType
{
    /** @var ContactFormSettingsProvider */
    private $contactFormSettings;

    public function __construct(
        ContactFormSettingsProvider $contactFormSettings
    ) {
        $this->contactFormSettings = $contactFormSettings;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'mango_sylius.contactForm.email',
                'attr' => [
                    'placeholder' => 'mango_sylius.contactForm.required',
                ],
                'required' => true,
            ])
            ->add('message', TextareaType::class, [
                'label' => '',
                'attr' => [
                    'placeholder' => 'mango_sylius.contactForm.message',
                    'required' => true,
                ],
            ]);

        if ($this->contactFormSettings->isNameRequired() !== false) {
            $builder
                ->add('senderName', TextType::class, [
                    'label' => 'mango_sylius.contactForm.senderName',
                    'attr' => [
                        'placeholder' => 'mango_sylius.contactForm.required',
                    ],
                    'required' => true,
                ]);
        } else {
            $builder
                ->add('senderName', TextType::class, [
                    'label' => 'mango_sylius.contactForm.senderName',
                    'required' => false,
                ]);
        }

        if ($this->contactFormSettings->isPhoneRequired() !== false) {
            $builder
                ->add('phone', TelType::class, [
                    'label' => 'mango_sylius.contactForm.phone',
                    'attr' => [
                        'placeholder' => 'mango_sylius.contactForm.required',
                    ],
                    'required' => true,
                ]);
        } else {
            $builder
                ->add('phone', TelType::class, [
                    'label' => 'mango_sylius.contactForm.phone',
                    'required' => false,
                ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mango_sylius_contact_form';
    }
}
