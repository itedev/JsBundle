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
     * @var string
     */
    private $content;

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
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return AjaxRequestEvent
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return boolean
     */
    public function hasContent()
    {
        return null !== $this->content;
    }

}