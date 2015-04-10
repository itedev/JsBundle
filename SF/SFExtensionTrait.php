<?php

namespace ITE\JsBundle\SF;

use ITE\Common\DependencyInjection\ExtensionTrait;
use ITE\JsBundle\EventListener\Event\AjaxRequestEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Class SFExtensionTrait
 *
 * @author  sam0delkin <t.samodelkin@gmail.com>
 */
trait SFExtensionTrait
{
    use ExtensionTrait;
    use AssetExtensionTrait;

    /**
     * {@inheritdoc}
     */
    public function getInlineJavascripts()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function onAjaxRequest(AjaxRequestEvent $event)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onAjaxResponse(FilterResponseEvent $event)
    {
    }
}