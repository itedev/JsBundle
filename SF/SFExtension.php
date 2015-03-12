<?php


namespace ITE\JsBundle\SF;

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
    public function addStylesheets()
    {
        return array();
    }

    /**
     * @return array
     */
    public function addJavascripts()
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
     * @param GetResponseForControllerResultEvent $event
     */
    public function onAjaxRequest(GetResponseForControllerResultEvent $event)
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