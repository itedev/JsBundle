<?php

namespace ITE\JsBundle\SF;


use ITE\JsBundle\EventListener\Event\AjaxRequestEvent;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Class SFExtensionTrait
 *
 * @package ITE\JsBundle\SF
 * @author  sam0delkin <t.samodelkin@gmail.com>
 */
trait SFExtensionTrait
{
    /**
     * @inheritdoc
     */
    public function getJavascripts()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getStylesheets()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function loadConfiguration(array $config, ContainerBuilder $container)
    {
        return;
    }

    /**
     * @inheritdoc
     */
    public function getConfiguration(ContainerBuilder $container)
    {
        return null;
    }


    /**
     * @inheritdoc
     */
    public function getInlineJavascripts()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function onAjaxRequest(AjaxRequestEvent $event)
    {
        return;
    }

    /**
     * @inheritdoc
     */
    public function onAjaxResponse(FilterResponseEvent $event)
    {
        return;
    }
}