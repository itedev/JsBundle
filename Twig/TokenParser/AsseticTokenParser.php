<?php

namespace ITE\JsBundle\Twig\TokenParser;

use Assetic\Asset\AssetInterface;
use Assetic\Factory\AssetFactory;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Bundle\AsseticBundle\Exception\InvalidBundleException;
use Symfony\Bundle\AsseticBundle\Twig\AsseticNode;
use Assetic\Extension\Twig\AsseticTokenParser as BaseAsseticTokenParser;

/**
 * Class AsseticTokenParser
 * @package ITE\JsBundle\Twig\TokenParser
 */
class AsseticTokenParser extends BaseAsseticTokenParser
{
    private $factory;
    private $output;
    private $single;
    private $extensions;

    private $templateNameParser;
    private $enabledBundles;

    /**
     * @inheritdoc
     */
    public function __construct(AssetFactory $factory, $tag, $output, $single = false, array $extensions = array())
    {
        $this->factory = $factory;
        $this->output = $output;
        $this->single = $single;
        $this->extensions = $extensions;

        parent::__construct($factory, $tag, $output, $output, $extensions);
    }

    /**
     * @inheritdoc
     */
    public function setTemplateNameParser(TemplateNameParserInterface $templateNameParser)
    {
        $this->templateNameParser = $templateNameParser;
    }

    /**
     * @inheritdoc
     */
    public function setEnabledBundles(array $enabledBundles = null)
    {
        $this->enabledBundles = $enabledBundles;
    }

    /**
     * @inheritdoc
     */
    public function parse(\Twig_Token $token)
    {
        if ($this->templateNameParser && is_array($this->enabledBundles)) {
            // check the bundle
            $templateRef = null;
            try {
                $templateRef = $this->templateNameParser->parse($this->parser->getStream()->getFilename());
            } catch (\RuntimeException $e) {
                // this happens when the filename isn't a Bundle:* url
                // and it contains ".."
            } catch (\InvalidArgumentException $e) {
                // this happens when the filename isn't a Bundle:* url
                // but an absolute path instead
            }
            $bundle = $templateRef ? $templateRef->get('bundle') : null;
            if ($bundle && !in_array($bundle, $this->enabledBundles)) {
                throw new InvalidBundleException($bundle, "the {% {$this->getTag()} %} tag", $templateRef->getLogicalName(), $this->enabledBundles);
            }
        }

        return $this->parentParse($token);
    }

    /**
     * @inheritdoc
     */
    protected function parentParse(\Twig_Token $token)
    {
        $inputs = array();
        $filters = array();
        $name = null;
        $attributes = array(
            'output'   => $this->output,
            'var_name' => 'asset_url',
            'vars'     => array(),
        );

        $stream = $this->parser->getStream();
        while (!$stream->test(\Twig_Token::BLOCK_END_TYPE)) {
            if ($stream->test(\Twig_Token::STRING_TYPE)) {
                // '@jquery', 'js/src/core/*', 'js/src/extra.js'
                $inputs[] = $stream->next()->getValue();
            } elseif ($stream->test(\Twig_Token::NAME_TYPE, 'ite_js_sf_assets')) {
                // ite_js_sf_assets()
                $expression = $this->parser->getExpressionParser()->parseExpression();
                $this->parser->getEnvironment()->compile($expression);
                $inputs = array_merge($inputs, call_user_func($expression->getAttribute('callable'), $token->getValue()));
            } elseif ($stream->test(\Twig_Token::NAME_TYPE, 'filter')) {
                // filter='yui_js'
                $stream->next();
                $stream->expect(\Twig_Token::OPERATOR_TYPE, '=');
                $filters = array_merge($filters, array_filter(array_map('trim', explode(',', $stream->expect(\Twig_Token::STRING_TYPE)->getValue()))));
            } elseif ($stream->test(\Twig_Token::NAME_TYPE, 'output')) {
                // output='js/packed/*.js' OR output='js/core.js'
                $stream->next();
                $stream->expect(\Twig_Token::OPERATOR_TYPE, '=');
                $attributes['output'] = $stream->expect(\Twig_Token::STRING_TYPE)->getValue();
            } elseif ($stream->test(\Twig_Token::NAME_TYPE, 'name')) {
                // name='core_js'
                $stream->next();
                $stream->expect(\Twig_Token::OPERATOR_TYPE, '=');
                $name = $stream->expect(\Twig_Token::STRING_TYPE)->getValue();
            } elseif ($stream->test(\Twig_Token::NAME_TYPE, 'as')) {
                // as='the_url'
                $stream->next();
                $stream->expect(\Twig_Token::OPERATOR_TYPE, '=');
                $attributes['var_name'] = $stream->expect(\Twig_Token::STRING_TYPE)->getValue();
            } elseif ($stream->test(\Twig_Token::NAME_TYPE, 'debug')) {
                // debug=true
                $stream->next();
                $stream->expect(\Twig_Token::OPERATOR_TYPE, '=');
                $attributes['debug'] = 'true' == $stream->expect(\Twig_Token::NAME_TYPE, array('true', 'false'))->getValue();
            } elseif ($stream->test(\Twig_Token::NAME_TYPE, 'combine')) {
                // combine=true
                $stream->next();
                $stream->expect(\Twig_Token::OPERATOR_TYPE, '=');
                $attributes['combine'] = 'true' == $stream->expect(\Twig_Token::NAME_TYPE, array('true', 'false'))->getValue();
            } elseif ($stream->test(\Twig_Token::NAME_TYPE, 'vars')) {
                // vars=['locale','browser']
                $stream->next();
                $stream->expect(\Twig_Token::OPERATOR_TYPE, '=');
                $stream->expect(\Twig_Token::PUNCTUATION_TYPE, '[');

                while ($stream->test(\Twig_Token::STRING_TYPE)) {
                    $attributes['vars'][] = $stream->expect(\Twig_Token::STRING_TYPE)->getValue();

                    if (!$stream->test(\Twig_Token::PUNCTUATION_TYPE, ',')) {
                        break;
                    }

                    $stream->next();
                }

                $stream->expect(\Twig_Token::PUNCTUATION_TYPE, ']');
            } elseif ($stream->test(\Twig_Token::NAME_TYPE, $this->extensions)) {
                // an arbitrary configured attribute
                $key = $stream->next()->getValue();
                $stream->expect(\Twig_Token::OPERATOR_TYPE, '=');
                $attributes[$key] = $stream->expect(\Twig_Token::STRING_TYPE)->getValue();
            } else {
                $token = $stream->getCurrent();
                throw new \Twig_Error_Syntax(sprintf('Unexpected token "%s" of value "%s"', \Twig_Token::typeToEnglish($token->getType(), $token->getLine()), $token->getValue()), $token->getLine());
            }
        }

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        $body = $this->parser->subparse(array($this, 'testEndTag'), true);

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        if ($this->single && 1 < count($inputs)) {
            $inputs = array_slice($inputs, -1);
        }

        if (!$name) {
            $name = $this->factory->generateAssetName($inputs, $filters, $attributes);
        }

        $asset = $this->factory->createAsset($inputs, $filters, $attributes + array('name' => $name));

        return $this->createNode($asset, $body, $inputs, $filters, $name, $attributes, $token->getLine(), $this->getTag());
    }

    /**
     * @inheritdoc
     */
    protected function createNode(AssetInterface $asset, \Twig_NodeInterface $body, array $inputs, array $filters, $name, array $attributes = array(), $lineno = 0, $tag = null)
    {
        return new AsseticNode($asset, $body, $inputs, $filters, $name, $attributes, $lineno, $tag);
    }

}