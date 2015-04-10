<?php

namespace ITE\JsBundle;

use ITE\JsBundle\DependencyInjection\Compiler\SFExtensionPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ITEJsBundle
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ITEJsBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new SFExtensionPass());

        parent::build($container);
    }
}
