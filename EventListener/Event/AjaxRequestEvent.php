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
     * @var array $ajaxData
     */
    private $ajaxData = [];

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

    /**
     * @return array
     */
    public function getAjaxData()
    {
        return $this->ajaxData;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function addAjaxData($name, $value)
    {
        if (array_key_exists($name, $this->ajaxData)) {
            throw new \RuntimeException(sprintf('Key "%s" already exists'));
        }

        $this->ajaxData[$name] = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasAjaxData()
    {
        return !empty($this->ajaxData);
    }
}