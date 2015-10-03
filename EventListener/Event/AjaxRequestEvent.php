<?php

namespace ITE\JsBundle\EventListener\Event;

use ITE\JsBundle\SF\AjaxDataBag;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;

/**
 * Class AjaxRequestEvent
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class AjaxRequestEvent extends AjaxEvent
{
    /**
     * @var string
     */
    private $content;

    /**
     * @var mixed
     */
    private $controllerResult;

    /**
     * {@inheritdoc}
     */
    public function __construct(KernelEvent $event, AjaxDataBag $ajaxDataBag)
    {
        parent::__construct($event, $ajaxDataBag);

        /** @var GetResponseForControllerResultEvent $event */
        $this->controllerResult = $event->getControllerResult();
    }

    /**
     * @return mixed
     */
    public function getControllerResult()
    {
        return $this->controllerResult;
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
