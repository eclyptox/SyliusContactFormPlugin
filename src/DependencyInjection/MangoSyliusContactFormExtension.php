<?php

declare(strict_types=1);

namespace MangoSylius\SyliusContactFormPlugin\DependencyInjection;

use MangoSylius\SyliusContactFormPlugin\Service\ContactFormSettingsProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class MangoSyliusContactFormExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition(ContactFormSettingsProvider::class);
        $definition->addArgument($config);
    }
}
