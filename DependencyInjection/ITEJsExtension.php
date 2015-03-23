<?php

namespace ITE\JsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Loader\FileLoader;

/**
 * Class ITEJsExtension
 * @package ITE\JsBundle\DependencyInjection
 */
class ITEJsExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('sf.yml');

        $this->loadAsseticConfiguration($loader, $config, $container);
        $this->loadAjaxBlockConfiguration($loader, $config, $container);
    }

    /**
     * @param FileLoader       $loader
     * @param array            $config
     * @param ContainerBuilder $container
     */
    protected function loadAjaxBlockConfiguration(FileLoader $loader, array $config, ContainerBuilder $container)
    {
        if ($config['ajax_content']['ajax_block']['enabled']) {
            $container->setParameter('ite_js.ajax_content.ajax_block.options', $config['ajax_content']['ajax_block']);
            $loader->load('ajax_block.yml');
        }
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
}
