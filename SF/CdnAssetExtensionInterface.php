<?php

namespace ITE\JsBundle\SF;

use ITE\Common\CdnJs\Resource\Reference;

/**
 * Interface CdnAssetExtensionInterface
 *
 * @author  sam0delkin <t.samodelkin@gmail.com>
 */
interface CdnAssetExtensionInterface
{
    /**
     * @param bool $debug
     *
     * @return Reference[]
     */
    public function getCdnJavascripts($debug);

    /**
     * @param bool $debug
     *
     * @return Reference[]
     */
    public function getCdnStylesheets($debug);

    /**
     * @return string
     */
    public function getCdnName();
}