<?php
/**
 * This file is created by sam0delkin (t.samodelkin@gmail.com).
 * IT-Excellence (http://itedev.com)
 * Date: 23.03.2015
 * Time: 14:46
 */

namespace ITE\JsBundle\EventListener\Event;


use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Class AjaxRequestEvent
 *
 * @package ITE\JsBundle\EventListener\Event
 */
class AjaxRequestEvent
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request|\Symfony\Component\HttpKernel\Event\Request
     */
    private $request;

    /**
     * @var \Symfony\Component\HttpFoundation\Response
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
     * @return \Symfony\Component\HttpFoundation\Request|\Symfony\Component\HttpKernel\Event\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
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