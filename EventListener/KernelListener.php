<?php

namespace ITE\JsBundle\EventListener;

use ITE\JsBundle\SF\SFInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class KernelListener
 * @package ITE\JsBundle\EventListener
 */
class KernelListener
{
    /**
     * @var SFInterface
     */
    protected $sf;

    /**
     * @param SFInterface $sf
     */
    public function __construct(SFInterface $sf)
    {
        $this->sf = $sf;
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if ($this->isSFAjaxRequest($event)) {
            $this->sf->onAjaxResponse($event);
        }
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        if ($this->isSFAjaxRequest($event)) {
            $this->sf->onAjaxRequest($event);
        }
    }

    /**
     * @param KernelEvent $event
     * @return bool
     */
    protected function isSFAjaxRequest(KernelEvent $event)
    {
        $request = $event->getRequest();
        return $event->isMasterRequest()
            && $request->isXmlHttpRequest()
            && $request->headers->has('X-SF-Ajax');
    }

}