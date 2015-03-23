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
    public function addStylesheets();

    /**
     * @return array
     */
    public function addJavascripts();

    /**
     * @return string
     */
    public function dump();

    /**
     * @param AjaxRequestEvent $event
     * @return array
     */
    public function getAjaxContent(AjaxRequestEvent $event);

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onAjaxRequest(GetResponseForControllerResultEvent $event);

    /**
     * @param FilterResponseEvent $event
     */
    public function onAjaxResponse(FilterResponseEvent $event);
}