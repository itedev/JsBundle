<?php

namespace ITE\JsBundle\SF;

use ITE\Common\CdnJs\Resource\Reference;
use ITE\Common\DependencyInjection\ExtensionInterface;
use ITE\JsBundle\EventListener\Event\AjaxRequestEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

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
     * @param FilterResponseEvent $event
     */
    public function onAjaxResponse(FilterResponseEvent $event);
}