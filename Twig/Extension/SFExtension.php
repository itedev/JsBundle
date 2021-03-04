<?php

namespace ITE\JsBundle\Twig\Extension;

use ITE\Common\CdnJs\CdnAssetReference;
use ITE\JsBundle\SF\SFInterface;
use Twig_Extension;

/**
 * Class SFExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class SFExtension extends Twig_Extension
{
    /**
     * @var SFInterface
     */
    protected $sf;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * @param SFInterface $sf
     * @param bool $debug
     */
    public function __construct(SFInterface $sf, $debug)
    {
        $this->sf = $sf;
        $this->debug = $debug;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('ite_sf_dump', [$this, 'dump'], ['pre_escape' => 'html', 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('ite_cdn_stylesheets', [$this, 'cdnStylesheets'], ['pre_escape' => 'html', 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('ite_cdn_javascripts', [$this, 'cdnJavascripts'], ['pre_escape' => 'html', 'is_safe' => ['html']]),
        ];
    }

    public function dump(bool $defer = false, bool $async = false): string
    {
        return $this->sf->dump($defer, $async);
    }

    /**
     * @param bool $debug
     * @return string
     */
    public function cdnStylesheets($debug = null)
    {
        $debug = null !== $debug ? $debug : $this->debug;

        $stylesheets = $this->sf->getCdnStylesheets($debug);
        $stylesheets = array_map(function (CdnAssetReference $stylesheet) {
            return sprintf('<link href="%s" type="text/css" rel="stylesheet" media="screen" />', (string) $stylesheet);
        }, $stylesheets);

        return implode('', $stylesheets);
    }

    /**
     * @param bool $debug
     * @return string
     */
    public function cdnJavascripts($debug = null)
    {
        $debug = null !== $debug ? $debug : $this->debug;

        $javascripts = $this->sf->getCdnJavascripts($debug);
        $javascripts = array_map(function (CdnAssetReference $javascript) {
            return sprintf('<script type="text/javascript" src="%s"></script>', (string) $javascript);
        }, $javascripts);

        return implode('', $javascripts);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ite_js.twig.extension.sf';
    }
}
