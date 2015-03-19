<?php
/**
 * This file is created by sam0delkin (t.samodelkin@gmail.com).
 * IT-Excellence (http://itedev.com)
 * Date: 12.03.2015
 * Time: 18:49
 */

namespace ITE\JsBundle\Twig\Extension;

use ITE\JsBundle\AjaxBlock\AjaxBlockStorage;
use ITE\JsBundle\Twig\TokenParser\AjaxBlockTokenParser;

/**
 * Class AjaxBlockExtension
 *
 * @package ITE\JsBundle\Twig\Extension
 */
class AjaxBlockExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getTokenParsers()
    {
        return array(
            new AjaxBlockTokenParser(),
        );
    }

    /**
     * @param string $blockName
     * @param string $content
     */
    public function addAjaxBlockContent($blockName, $content)
    {
        AjaxBlockStorage::addAjaxBlock($blockName, $content);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'ite_js.twig.ajax_block_extension';
    }

}