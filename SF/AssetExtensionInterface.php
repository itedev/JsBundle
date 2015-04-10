<?php

namespace ITE\JsBundle\SF;

/**
 * Interface AssetExtensionInterface
 *
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