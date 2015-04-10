<?php

namespace ITE\JsBundle\SF;

use ITE\Common\CdnJs\Resource\Reference;
use ITE\JsBundle\EventListener\Event\AjaxRequestEvent;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeParentInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Class SFExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class SFExtension implements SFExtensionInterface
{
    use SFExtensionTrait;
}