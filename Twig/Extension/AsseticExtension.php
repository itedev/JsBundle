<?php

namespace ITE\JsBundle\Twig\Extension;

use Assetic\Factory\AssetFactory;
use Symfony\Component\Templating\TemplateNameParserInterface;
use ITE\JsBundle\Twig\TokenParser\AsseticTokenParser;
use Assetic\ValueSupplierInterface;
use Symfony\Bundle\AsseticBundle\Twig\AsseticExtension as BaseAsseticExtension;

/**
 * Class AsseticExtension
 * @package ITE\JsBundle\Twig\Extension
 */
class AsseticExtension extends BaseAsseticExtension
{
    protected $useController;
    protected $templateNameParser;
    protected $enabledBundles;

    /**
     * @inheritdoc
     */
    public function __construct(AssetFactory $factory, TemplateNameParserInterface $templateNameParser, $useController = false, $functions = array(), $enabledBundles = array(), ValueSupplierInterface $valueSupplier = null)
    {
        $this->useController = $useController;
        $this->templateNameParser = $templateNameParser;
        $this->enabledBundles = $enabledBundles;

        parent::__construct($factory, $templateNameParser, $useController, $functions, $enabledBundles, $valueSupplier);
    }

    /**
     * @inheritdoc
     */
    public function getTokenParsers()
    {
        return array(
            $this->createTokenParser('javascripts', 'js/*.js'),
            $this->createTokenParser('stylesheets', 'css/*.css'),
            $this->createTokenParser('image', 'images/*', true),
        );
    }

    /**
     * @inheritdoc
     */
    protected function createTokenParser($tag, $output, $single = false)
    {
        $tokenParser = new AsseticTokenParser($this->factory, $tag, $output, $single, array('package'));
        $tokenParser->setTemplateNameParser($this->templateNameParser);
        $tokenParser->setEnabledBundles($this->enabledBundles);

        return $tokenParser;
    }
} 