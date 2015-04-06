<?php

namespace ITE\JsBundle\SF;

use ITE\JsBundle\EventListener\Event\AjaxRequestEvent;
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
     * @return array
     */
    public function getJavascripts()
    {
        return array();
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
} 