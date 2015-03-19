<?php
/**
 * This file is created by sam0delkin (t.samodelkin@gmail.com).
 * IT-Excellence (http://itedev.com)
 * Date: 19.03.2015
 * Time: 13:14
 */

namespace ITE\JsBundle\AjaxContent;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Interface AjaxContentExtensionInterface
 *
 * @package ITE\JsBundle\AjaxContent
 */
interface AjaxContentExtensionInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param GetResponseForControllerResultEvent $event
     * @return array
     */
    public function getDataForAjaxResponse(GetResponseForControllerResultEvent $event);

    /**
     * @return array
     */
    public function addJavascripts();

    /**
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request);
}