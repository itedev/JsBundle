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
     * @param $extensionName
     * @return bool
     */
    public function hasExtension($extensionName);

    /**
     * @param $extensionName
     * @param SFExtensionInterface $extension
     * @return SFInterface
     */
    public function addExtension($extensionName, SFExtensionInterface $extension);

    /**
     * @param $extensionName
     * @return SFExtensionInterface
     * @throws InvalidArgumentException
     */
    public function getExtension($extensionName);

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
     * @return ParameterBagInterface
     */
    public function getParameterBag();

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event);

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event);
}