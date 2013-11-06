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
//            $data = '';
//            if ($response instanceof JsonResponse) {
//                /** @var $response JsonResponse */
//                $data = json_decode($response->getContent(), true);
//            } elseif ($response instanceof RedirectResponse) {
//                /** @var $response RedirectResponse */
//                $this->sf->getParameterBag()->set('targetUrl', $response->getTargetUrl());
//            } elseif ($response instanceof Response) {
//                /** @var $response Response */
//                $data = $response->getContent();
//                if ('json' === $format) {
//                    $data = json_decode($data, true);
//                }
//            }

            $this->sf->onKernelResponse($event);
        }
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        if ($this->isSFAjaxRequest($event)) {
            $this->sf->onKernelView($event);
        }
    }

    /**
     * @param KernelEvent $event
     * @return bool
     */
    protected function isSFAjaxRequest(KernelEvent $event)
    {
        $request = $event->getRequest();
        return HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()
            && $request->isXmlHttpRequest()
            && $request->headers->has('X-SF-Ajax');
    }

}