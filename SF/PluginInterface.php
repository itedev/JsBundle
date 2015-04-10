<?php

namespace ITE\JsBundle\SF;


use ITE\Common\DependencyInjection\ExtensionInterface;

/**
 * Interface PluginInterface
 *
 * @package ITE\JsBundle\SF
 * @author  sam0delkin <t.samodelkin@gmail.com>
 */
interface PluginInterface extends AssetExtensionInterface, CdnAssetExtensionInterface, ExtensionInterface
{
}