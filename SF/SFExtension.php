<?php

namespace ITE\JsBundle\SF;

use ITE\Common\CdnJs\Resource\Reference;
use ITE\JsBundle\EventListener\Event\AjaxRequestEvent;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Class SFExtension
 * @package ITE\JsBundle\SF
 */
class SFExtension implements SFExtensionInterface
{
    /**
     * @return array
     */
    public function getStylesheets()
    {
        return array();
    }

    /**
     * @param bool $debug
     * @return \ITE\Common\CdnJs\Resource\Reference[]
     */
    public function getCdnStylesheets($debug)
    {
        return [];
    }

    /**
     * @return array
     */
    public function getJavascripts()
    {
        return array();
    }

    /**
     * @param bool $debug
     * @return \ITE\Common\CdnJs\Resource\Reference[]
     */
    public function getCdnJavascripts($debug)
    {
        return [];
    }

    /**
     * @return string
     */
    public function dump()
    {
        return '';
    }

    /**
     * @param AjaxRequestEvent $event
     */
    public function onAjaxRequest(AjaxRequestEvent $event)
    {
        return;
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onAjaxResponse(FilterResponseEvent $event)
    {
        return;
    }

    /**
     * @inheritdoc
     */
    public function loadConfiguration(array $config, ContainerBuilder $container)
    {

    }

    /**
     * @inheritdoc
     */
    public function addConfiguration(ArrayNodeDefinition $pluginsNode, ContainerBuilder $container)
    {

    }


} 