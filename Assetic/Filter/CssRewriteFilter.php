<?php

namespace ITE\JsBundle\Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\CssRewriteFilter as BaseCssRewriteFilter;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class CssRewriteFilter
 * @package ITE\JsBundle\Assetic\Filter
 */
class CssRewriteFilter extends BaseCssRewriteFilter
{
    /**
     * @var KernelInterface $kernel
     */
    protected $kernel;

    /**
     * @var array|null $bundleMap
     */
    protected $bundleMap;

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    public function filterDump(AssetInterface $asset)
    {
        $sourceBase = $asset->getSourceRoot();
        $sourcePath = $asset->getSourcePath();
        $targetPath = $asset->getTargetPath();

        if (null === $sourcePath || null === $targetPath || $sourcePath == $targetPath) {
            return;
        }

        if (null !== $bundle = $this->getBundleByPath($sourceBase)) {
            $bundleDir = 'bundles/' . strtolower(substr($bundle->getName(), 0, -6));
            $sourcePath = str_replace('Resources/public', $bundleDir, $sourcePath);
        }

        // learn how to get from the target back to the source
        if (false !== strpos($sourceBase, '://')) {
            list($scheme, $url) = explode('://', $sourceBase.'/'.$sourcePath, 2);
            list($host, $path) = explode('/', $url, 2);

            $host = $scheme.'://'.$host.'/';
            $path = false === strpos($path, '/') ? '' : dirname($path);
            $path .= '/';
        } else {
            // assume source and target are on the same host
            $host = '';

            // pop entries off the target until it fits in the source
            if ('.' == dirname($sourcePath)) {
                $path = str_repeat('../', substr_count($targetPath, '/'));
            } elseif ('.' == $targetDir = dirname($targetPath)) {
                $path = dirname($sourcePath).'/';
            } else {
                $path = '';
                while (0 !== strpos($sourcePath, $targetDir)) {
                    if (false !== $pos = strrpos($targetDir, '/')) {
                        $targetDir = substr($targetDir, 0, $pos);
                        $path .= '../';
                    } else {
                        $targetDir = '';
                        $path .= '../';
                        break;
                    }
                }
                $path .= ltrim(substr(dirname($sourcePath).'/', strlen($targetDir)), '/');
            }
        }

        $self = $this;
        $content = $this->filterReferences($asset->getContent(), function($matches) use ($host, $path, $asset, $bundle, $self) {
            if (false !== strpos($matches['url'], '://') || 0 === strpos($matches['url'], '//') || 0 === strpos($matches['url'], 'data:')) {
                // absolute or protocol-relative or data uri
                return $matches[0];
            }

            if (isset($matches['url'][0]) && '/' == $matches['url'][0]) {
                // root relative
                return str_replace($matches['url'], $host.$matches['url'], $matches[0]);
            }

            // document relative
            $url = $matches['url'];
            while (0 === strpos($url, '../') && 2 <= substr_count($path, '/')) {
                $path = substr($path, 0, strrpos(rtrim($path, '/'), '/') + 1);
                $url = substr($url, 3);
            }

            $parts = array();
            foreach (explode('/', $host.$path.$url) as $part) {
                if ('..' === $part && count($parts) && '..' !== end($parts)) {
                    array_pop($parts);
                } else {
                    $parts[] = $part;
                }
            }

            // probably not quite correct code below
            if (null !== $bundle) {
                $refName = basename($matches['url']);
                $refUrl = realpath($asset->getSourceDirectory() . '/' . dirname($matches['url'])) . '/' . $refName;
                if (preg_match('~(.+)/Resources/public/(.+)~', $refUrl, $refMatches)) {
                    if (null !== $refBundle = $self->getBundleByPath($refMatches[1])) {
                        if ($bundle->getPath() !== $refBundle->getPath()) {
                            $refBundleDir = 'bundles/' . strtolower(substr($refBundle->getName(), 0, -6));
                            $refSourcePath = $refMatches[2];

                            return str_replace($matches['url'], $path . $refBundleDir . '/' . $refSourcePath, $matches[0]);
                        }
                    }
                }
            }

            return str_replace($matches['url'], implode('/', $parts), $matches[0]);
        });

        $asset->setContent($content);
    }

    /**
     * @param $path
     * @return BundleInterface|null
     */
    public function getBundleByPath($path)
    {
        $bundleMap = $this->getBundleMap();
        if (array_key_exists($path, $bundleMap)) {
            return $this->bundleMap[$path];
        }

        return null;
    }

    /**
     * @return BundleInterface[]
     */
    protected function getBundleMap()
    {
        if (isset($this->bundleMap)) {
            return $this->bundleMap;
        }

        $this->bundleMap = array();
        foreach ($this->kernel->getBundles() as $bundle) {
            /** @var $bundle BundleInterface */
            $this->bundleMap[$bundle->getPath()] = $bundle;
        }

        return $this->bundleMap;
    }
} 