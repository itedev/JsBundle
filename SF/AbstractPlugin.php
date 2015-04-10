<?php

namespace ITE\JsBundle\SF;

use ITE\JsBundle\EventListener\Event\AjaxRequestEvent;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Class AbstractPlugin
 *
 * @author  sam0delkin <t.samodelkin@gmail.com>
 */
abstract class AbstractPlugin implements PluginInterface
{
    use PluginTrait;
}