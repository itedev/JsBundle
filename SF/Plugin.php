<?php

namespace ITE\JsBundle\SF;

use ITE\JsBundle\EventListener\Event\AjaxRequestEvent;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Class Plugin
 *
 * @package ITE\JsBundle\SF
 * @author  sam0delkin <t.samodelkin@gmail.com>
 */
class Plugin implements PluginInterface
{
    use PluginTrait;
}