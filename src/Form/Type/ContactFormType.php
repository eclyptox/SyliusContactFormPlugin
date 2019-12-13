<?php

declare(strict_types=1);

namespace MangoSylius\SyliusContactFormPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('senderName', TextType::class, [
                'label' => 'mango_sylius.contactForm.senderName',
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'mango_sylius.contactForm.email',
                'required' => true,
            ])
            ->add('phone', TelType::class, [
                'label' => 'mango_sylius.contactForm.phone',
                'required' => true,
            ])
            ->add('message', TextareaType::class, [
                'label' => '',
                'required' => true,
                'attr' => [
                    'placeholder' => 'mango_sylius.contactForm.message',
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mango_sylius_contact_form';
    }
}
