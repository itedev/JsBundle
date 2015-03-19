<?php
/**
 * This file is created by sam0delkin (t.samodelkin@gmail.com).
 * IT-Excellence (http://itedev.com)
 * Date: 19.03.2015
 * Time: 15:51
 */

namespace ITE\JsBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class AjaxContentExtensionPass
 *
 * @package ITE\JsBundle\DependencyInjection\Compiler
 */
class AjaxContentExtensionPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ite_js.ajax_content.extension_manager')) {
            return;
        }

        $definition = $container->getDefinition('ite_js.ajax_content.extension_manager');

        $taggedServices = $container->findTaggedServiceIds('ite_js.ajax_content.extension');
        foreach ($taggedServices as $id => $tagAttributes) {
            $definition->addMethodCall('addExtension', array(new Reference($id)));
        }
    }

}