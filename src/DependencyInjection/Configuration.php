<?php

declare(strict_types=1);

namespace MangoSylius\SyliusContactFormPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('mango_sylius_contact_form_plugin');
        if (\method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('mango_sylius_contact_form_plugin');
        }

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('send_manager_mail')->defaultFalse()->end()
                ->booleanNode('send_customer_mail')->defaultFalse()->end()
                ->booleanNode('name_required')->defaultFalse()->end()
                ->booleanNode('phone_required')->defaultFalse()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
