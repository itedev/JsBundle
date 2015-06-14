<?php

namespace ITE\JsBundle\EventListener\Event;

use ITE\JsBundle\SF\AjaxDataBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;

/**
 * Class AjaxEvent
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class AjaxEvent
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
     * @var AjaxDataBag
     */
    private $ajaxDataBag;

    /**
     * @param KernelEvent $event
     * @param AjaxDataBag $ajaxDataBag
     */
    public function __construct(KernelEvent $event, AjaxDataBag $ajaxDataBag)
    {
        $this->request = $event->getRequest();
        $this->ajaxDataBag = $ajaxDataBag;
        if ($event instanceof GetResponseEvent) {
            $this->response = $event->getResponse();
        } elseif ($event instanceof FilterResponseEvent) {
            $this->response = $event->getResponse();
        }
    }

    /**
     * Get request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get response
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return AjaxDataBag
     */
    public function getAjaxDataBag()
    {
        return $this->ajaxDataBag;
    }
}