<?php

namespace ITE\JsBundle;

use ITE\JsBundle\DependencyInjection\Compiler\SFCompilerPass;
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
        $container->addCompilerPass(new SFCompilerPass());
        parent::build($container);
    }
}
