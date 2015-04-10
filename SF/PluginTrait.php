<?php

namespace ITE\JsBundle\SF;

/**
 * Class PluginTrait
 *
 * @package ITE\JsBundle\SF
 * @author  sam0delkin <t.samodelkin@gmail.com>
 */
trait PluginTrait
{
    use SFExtensionTrait;

    /**
     * @inheritdoc
     */
    public function getCdnJavascripts($debug)
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getCdnStylesheets($debug)
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getCdnName()
    {
        return '';
    }
}