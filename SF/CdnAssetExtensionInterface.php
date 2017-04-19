<?php

namespace ITE\JsBundle\SF;

use ITE\Common\CdnJs\CdnAssetReference;

/**
 * Interface CdnAssetExtensionInterface
 *
 * @author  sam0delkin <t.samodelkin@gmail.com>
 */
interface CdnAssetExtensionInterface
{
    /**
     * @param bool $debug
     * @return array|CdnAssetReference[]
     */
    public function getCdnJavascripts($debug);

    /**
     * @param bool $debug
     * @return array|CdnAssetReference[]
     */
    public function getCdnStylesheets($debug);

    /**
     * @return string
     */
    public function getCdnName();
}
