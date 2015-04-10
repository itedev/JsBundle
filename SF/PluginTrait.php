<?php

namespace ITE\JsBundle\SF;

use ITE\Common\DependencyInjection\ExtensionTrait;

/**
 * Class PluginTrait
 *
 * @author  sam0delkin <t.samodelkin@gmail.com>
 */
trait PluginTrait
{
    use ExtensionTrait;
    use AssetExtensionTrait;
    use CdnAssetExtensionTrait;
}