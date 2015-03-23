<?php

namespace ITE\JsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package ITE\JsBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ite_js');

        $this->addAsseticConfiguration($rootNode);
        $this->addExtensionsConfiguration($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    protected function addAsseticConfiguration(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('assetic')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('cssrewrite')
                            ->canBeEnabled()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     * @return ArrayNodeDefinition
     */
    protected function addExtensionsConfiguration(ArrayNodeDefinition $rootNode)
    {
        $node = $rootNode
            ->children()
                ->arrayNode('extensions')
                    ->addDefaultsIfNotSet();

        $this->addAjaxBlockConfiguration($node);
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    protected function addAjaxBlockConfiguration(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('ajax_block')
                    ->canBeEnabled()
                    ->children()
                        ->arrayNode('show_animation')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->enumNode('type')
                                    ->defaultValue('show')
                                    ->values(array('show', 'slide', 'fade'))
                                    ->info('animation type')
                                ->end()
                                ->integerNode('length')
                                    ->defaultValue(0)
                                    ->min(0)
                                    ->info('time in ms')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
