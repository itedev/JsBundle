<?php

namespace ITE\JsBundle\SF;

use ITE\Common\DependencyInjection\ExtensionInterface;
use ITE\JsBundle\EventListener\Event\AjaxRequestEvent;
use ITE\JsBundle\EventListener\Event\AjaxResponseEvent;

/**
 * Interface SFExtensionInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface SFExtensionInterface extends ExtensionInterface, AssetExtensionInterface
{
    /**
     * @return string
     */
    public function getInlineJavascripts();

    /**
     * @param AjaxRequestEvent $event
     */
    public function onAjaxRequest(AjaxRequestEvent $event);

    /**
     * @param AjaxResponseEvent $event
     */
    public function onAjaxResponse(AjaxResponseEvent $event);
}