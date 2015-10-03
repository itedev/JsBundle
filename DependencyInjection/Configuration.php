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
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @param ContainerBuilder $container
     */
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

        $container = $this->container;
        $iteDir = __DIR__.'/../../../../';
        ExtensionFinder::loadExtensions(
            function (SFExtensionInterface $extension) use ($node, $container) {
                $config = $extension->getConfiguration($container);
                if ($config) {
                    $node->append($config);
                }
            },
            $iteDir,
            'ITE\JsBundle\SF\SFExtensionInterface',
            __DIR__.'/../'
        );
    }
}
