<?php

namespace ITE\JsBundle\SF;

/**
 * Trait CdnAssetExtensionTrait
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
trait CdnAssetExtensionTrait
{
    /**
     * {@inheritdoc}
     */
    public function getCdnJavascripts($debug)
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getCdnStylesheets($debug)
    {
        return [];
    }
}
