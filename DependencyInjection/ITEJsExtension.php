<?php

namespace ITE\JsBundle\DependencyInjection;

use ITE\Common\Extension\ExtensionFinder;
use ITE\JsBundle\SF\SFExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Loader\FileLoader;

/**
 * Class ITEJsExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ITEJsExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration($container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('sf.yml');

        $this->loadAsseticConfiguration($loader, $config, $container);
        $this->loadExtensions($config, $container);
    }

    /**
     * @param FileLoader $loader
     * @param array $config
     * @param ContainerBuilder $container
     */
    protected function loadAsseticConfiguration(FileLoader $loader, array $config, ContainerBuilder $container)
    {
        if ($config['assetic']['cssrewrite']['enabled']) {
            $loader->load('assetic/cssrewrite.yml');
        }
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    protected function loadExtensions(array $config, ContainerBuilder $container)
    {
        $iteDir = __DIR__.'/../../../../';
        ExtensionFinder::loadExtensions(
            function (SFExtensionInterface $extension) use ($config, $container) {
                $extension->loadConfiguration($config, $container);
            },
            $iteDir,
            'ITE\JsBundle\SF\SFExtensionInterface',
            __DIR__.'/../'
        );
    }

    /**
     * @inheritdoc
     */
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration($container);
    }


}
