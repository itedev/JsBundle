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
     * @param array $inputs
     * @return array
     */
    public function modifyStylesheets(array &$inputs)
    {
        return $inputs;
    }

    /**
     * @param array $inputs
     * @return array
     */
    public function modifyJavascripts(array &$inputs)
    {
        return $inputs;
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
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        return;
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        return;
    }
} 