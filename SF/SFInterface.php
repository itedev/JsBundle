<?php

namespace ITE\JsBundle\SF;

use InvalidArgumentException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Interface SFInterface
 * @package ITE\JsBundle\SF
 */
interface SFInterface
{
    /**
     * @param $name
     * @return bool
     */
    public function hasExtension($name);

    /**
     * @param $name
     * @param SFExtensionInterface $extension
     * @return SFInterface
     */
    public function addExtension($name, SFExtensionInterface $extension);

    /**
     * @param $name
     * @return SFExtensionInterface
     * @throws InvalidArgumentException
     */
    public function getExtension($name);

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
     * @param GetResponseForControllerResultEvent $event
     */
    public function onAjaxRequest(GetResponseForControllerResultEvent $event);

    /**
     * @param FilterResponseEvent $event
     */
    public function onAjaxResponse(FilterResponseEvent $event);
}