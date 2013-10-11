<?php

namespace ITE\JsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SFCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ite_js.sf')) {
            return;
        }

        $definition = $container->getDefinition(
            'ite_js.sf'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'ite_js.sf.extension'
        );

        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'addExtension',
                    array($attributes['alias'], new Reference($id))
                );
            }
        }
    }
}