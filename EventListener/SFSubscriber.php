<?php

namespace ITE\JsBundle\EventListener;

use ITE\JsBundle\EventListener\Event\FilterAssetEvent;
use ITE\JsBundle\EventListener\Event\SFEvents;
use ITE\JsBundle\SF\SFInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class SFSubscriber
 * @package ITE\JsBundle\EventListener
 */
class SFSubscriber implements EventSubscriberInterface
{
    /**
     * @var SFInterface
     */
    protected $sf;

    /**
     * @var string
     */
    protected $templates;

    /**
     * @param SFInterface $sf
     * @param $templates
     */
    public function __construct(SFInterface $sf, $templates)
    {
        $this->sf = $sf;
        $this->templates = $templates;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            SFEvents::MODIFY_ASSETS => 'onModifyAssets',
        );
    }

    /**
     * @param FilterAssetEvent $event
     */
    public function onModifyAssets(FilterAssetEvent $event)
    {
        if (!in_array($event->getFilename(), $this->templates)) {
            return;
        }

        if ('stylesheets' === $event->getTag()) {
            $inputs = $this->sf->modifyStylesheets($event->getInputs());
        } else {
            $inputs = $this->sf->modifyJavascripts($event->getInputs());
        }

        $event->setInputs($inputs);
    }
} 