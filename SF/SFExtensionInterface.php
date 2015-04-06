<?php

namespace ITE\JsBundle\SF;

use ITE\JsBundle\EventListener\Event\AjaxRequestEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Interface SFExtensionInterface
 * @package ITE\JsBundle\SF
 */
interface SFExtensionInterface
{
    /**
     * @return array
     */
    public function getStylesheets();

    /**
     * @return array
     */
    public function getJavascripts();

    /**
     * @return string
     */
    public function dump();

    /**
     * @param AjaxRequestEvent $event
     */
    public function onAjaxRequest(AjaxRequestEvent $event);

    /**
     * @param FilterResponseEvent $event
     */
    public function onAjaxResponse(FilterResponseEvent $event);
}