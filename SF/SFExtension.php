<?php

namespace ITE\JsBundle\SF;

use ITE\JsBundle\EventListener\Event\AjaxRequestEvent;
use ITE\JsBundle\EventListener\Event\AjaxResponseEvent;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Class SFExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class SFExtension implements SFExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfiguration(ContainerBuilder $container)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function loadConfiguration(array $config, ContainerBuilder $container)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getJavascripts()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getStylesheets()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getCdnStylesheets($debug)
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getCdnJavascripts($debug)
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function dump()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function onAjaxRequest(AjaxRequestEvent $event)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onAjaxResponse(AjaxResponseEvent $event)
    {
    }
}
