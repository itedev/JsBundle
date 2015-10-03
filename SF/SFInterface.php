<?php

namespace ITE\JsBundle\SF;

use ITE\Common\CdnJs\CdnAssetReference;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Interface SFInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
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
     * @throws \InvalidArgumentException
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
     * @param bool $debug
     * @return array|CdnAssetReference[]
     */
    public function getCdnStylesheets($debug);

    /**
     * @param bool $debug
     * @return array|CdnAssetReference[]
     */
    public function getCdnJavascripts($debug);

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
