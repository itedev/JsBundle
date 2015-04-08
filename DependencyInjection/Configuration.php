<?php

namespace ITE\JsBundle\DependencyInjection;

use ITE\Common\Extension\ExtensionFinder;
use ITE\JsBundle\SF\SFExtensionInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class Configuration
 * @package ITE\JsBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    public function __construct(ContainerBuilder $container)
    {
        $this->container = $container;
    }


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

        $container = $this->container;
        $iteDir = __DIR__.'/../../../../';
        ExtensionFinder::loadExtensions(
            function (SFExtensionInterface $extension) use ($rootNode, $container) {
                $extension->addConfiguration($rootNode, $container);
            },
            $iteDir,
            'ITE\JsBundle\SF\SFExtensionInterface',
            __DIR__.'/../'
        );
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
