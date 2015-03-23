<?php
/**
 * This file is created by sam0delkin (t.samodelkin@gmail.com).
 * IT-Excellence (http://itedev.com)
 * Date: 23.03.2015
 * Time: 14:46
 */

namespace ITE\JsBundle\EventListener\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Class AjaxRequestEvent
 *
 * @package ITE\JsBundle\EventListener\Event
 */
class AjaxRequestEvent
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Response
     */
    private $response;

    /**
     * @var mixed
     */
    private $controllerResult;

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function __construct(GetResponseForControllerResultEvent $event)
    {
        $this->request = $event->getRequest();
        $this->response = $event->getResponse();
        $this->controllerResult = $event->getControllerResult();
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return mixed
     */
    public function getControllerResult()
    {
        return $this->controllerResult;
    }


}