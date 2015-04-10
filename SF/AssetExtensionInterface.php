<?php

namespace ITE\JsBundle\SF;

/**
 * Interface AssetExtensionInterface
 *
 * @package ITE\JsBundle\SF
 * @author  sam0delkin <t.samodelkin@gmail.com>
 */
interface AssetExtensionInterface
{
    /**
     * @return []
     */
    public function getJavascripts();

    /**
     * @return []
     */
    public function getStylesheets();
}