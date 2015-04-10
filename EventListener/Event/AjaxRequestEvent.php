<?php

namespace ITE\JsBundle\EventListener\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Class AjaxRequestEvent
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
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
     * @var bool
     */
    private $responseOverridden = false;

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
        $this->request          = $event->getRequest();
        $this->response         = $event->getResponse();
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
     * @param mixed  $value
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

    /**
     * @param Response $response
     */
    public function overrideResponse(Response $response)
    {
        if ($this->responseOverridden) {
            throw new \InvalidArgumentException('Response has already been overridden.');
        }

        $this->response          = $response;
        $this->responseOverridden = true;
    }

    /**
     * @return boolean
     */
    public function isResponseOverridden()
    {
        return $this->responseOverridden;
    }

}