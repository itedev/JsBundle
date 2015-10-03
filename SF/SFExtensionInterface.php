<?php

namespace ITE\JsBundle\SF;

use ITE\Common\CdnJs\CdnAssetReference;
use ITE\Common\DependencyInjection\ExtensionInterface;
use ITE\JsBundle\EventListener\Event\AjaxRequestEvent;
use ITE\JsBundle\EventListener\Event\AjaxResponseEvent;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Interface SFExtensionInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface SFExtensionInterface extends ExtensionInterface
{
    /**
     * @param ContainerBuilder $container
     * @return NodeDefinition
     */
    public function getConfiguration(ContainerBuilder $container);

    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    public function loadConfiguration(array $config, ContainerBuilder $container);

    /**
     * @return array
     */
    public function getJavascripts();

    /**
     * @return array
     */
    public function getStylesheets();

    /**
     * @param bool $debug
     * @return array|CdnAssetReference[]
     */
    public function getCdnStylesheets($debug);

    /**
     * @param bool $debug
     * @return array|CdnAssetReference[]
     */
    public function getCdnJavascripts($debug);

    /**
     * @return string
     */
    public function dump();

    /**
     * @param AjaxRequestEvent $event
     */
    public function onAjaxRequest(AjaxRequestEvent $event);

    /**
     * @param AjaxResponseEvent $event
     */
    public function onAjaxResponse(AjaxResponseEvent $event);
}
