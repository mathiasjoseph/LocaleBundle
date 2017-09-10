<?php

namespace Miky\Bundle\LocaleBundle\DependencyInjection;

use Miky\Bundle\LocaleBundle\Doctrine\Entity\Language;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    private $useDefaultEntities;

    /**
     * Configuration constructor.
     */
    public function __construct($useDefaultEntities)
    {
        $this->useDefaultEntities = $useDefaultEntities;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('miky_locale');

        $rootNode
            ->children()
            ->scalarNode('default_locale')->isRequired()->cannotBeEmpty()->end()
            ->arrayNode('available_locales')
            ->prototype('scalar')
            ->cannotBeEmpty()
            ->isRequired()
            ->end()
            ->end();
        if ($this->useDefaultEntities){
            $rootNode
                ->children()
                ->scalarNode('language_class')->defaultValue(Language::class)->cannotBeEmpty()->end()
                ->end();
        }else{
            $rootNode
                ->children()
                ->scalarNode('language_class')->isRequired()->cannotBeEmpty()->end()
                ->end();
        }



        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
