<?php

namespace ITE\JsBundle\DependencyInjection\Compiler;

use ITE\Common\Extension\ExtensionFinder;
use ITE\JsBundle\SF\SFExtensionInterface;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Finder\Finder;

/**
 * Class SFExtensionPass
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class SFExtensionPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ite_js.sf')) {
            return;
        }

        $definition = $container->getDefinition('ite_js.sf');

        $taggedServices = $container->findTaggedServiceIds('ite_js.sf.extension');
        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall('addExtension', array($attributes['alias'], new Reference($id)));
            }
        }

        // load translations from SF extensions
        $this->loadTranslations($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function loadTranslations(ContainerBuilder $container)
    {
        $translator = $container->findDefinition('translator.default');
        $iteDir = __DIR__.'/../../../../../';
        ExtensionFinder::loadExtensions(
            function (SFExtensionInterface $extension) use ($container, $translator) {
                $refObj = new \ReflectionObject($extension);
                $dir = dirname($refObj->getFileName()) . '/../Resources/translations';
                if (!is_dir($dir)) {
                    return;
                }

                $container->addResource(new DirectoryResource($dir));
                $finder = Finder::create()
                    ->files()
                    ->filter(function (\SplFileInfo $file) {
                        return 2 === substr_count($file->getBasename(), '.') && preg_match('/\.\w+$/', $file->getBasename());
                    })
                    ->in($dir)
                ;

                foreach ($finder as $file) {
                    // filename is domain.locale.format
                    list($domain, $locale, $format) = explode('.', $file->getBasename(), 3);
                    $translator->addMethodCall('addResource', array($format, (string) $file, $locale, $domain));
                }
            },
            $iteDir,
            'ITE\JsBundle\SF\SFExtensionInterface',
            __DIR__.'/../../'
        );
    }
}
