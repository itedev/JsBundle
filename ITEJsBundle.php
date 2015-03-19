<?php

namespace ITE\JsBundle;

use ITE\JsBundle\DependencyInjection\Compiler\AjaxContentExtensionPass;
use ITE\JsBundle\DependencyInjection\Compiler\SFExtensionPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ITEJsBundle
 * @package ITE\JsBundle
 */
class ITEJsBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new SFExtensionPass());
        $container->addCompilerPass(new AjaxContentExtensionPass());
        parent::build($container);
    }
}
