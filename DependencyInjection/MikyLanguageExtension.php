<?php

namespace Miky\Bundle\LanguageBundle\DependencyInjection;

use Miky\Bundle\CoreBundle\DependencyInjection\AbstractCoreExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class MikyLanguageExtension extends AbstractCoreExtension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration($container->getParameter("miky_language.use_default_entities"));

        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $this->remapParametersNamespaces($config, $container, array(
            '' => array(
                'language_class' => 'miky_language.model.language.class',
            ),
        ));
    }

    public function prepend(ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/app'));
        $loader->load('config.yml');
    }
}
