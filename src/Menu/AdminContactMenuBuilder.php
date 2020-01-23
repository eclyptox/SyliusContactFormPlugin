<?php

declare(strict_types=1);

namespace MangoSylius\SyliusContactFormPlugin\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminContactMenuBuilder
{
    public function buildMenu(MenuBuilderEvent $event): void
    {
        $customer = $event
            ->getMenu()
            ->getChild('customers');

        if ($customer !== null) {
            $customer
                ->addChild('contact', [
                    'route' => 'mango_contact_form_plugin_admin_message_index',
                ])
                ->setName('mango_sylius.contactForm.ui.title')
                ->setLabelAttribute('icon', 'comments');
        }
    }
}
