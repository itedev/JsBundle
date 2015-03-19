<?php
/**
 * This file is created by sam0delkin (t.samodelkin@gmail.com).
 * IT-Excellence (http://itedev.com)
 * Date: 19.03.2015
 * Time: 13:21
 */

namespace ITE\JsBundle\AjaxContent;

/**
 * Class ExtensionManager
 *
 * @package ITE\JsBundle\AjaxContent
 */
class ExtensionManager
{
    /**
     * @var AjaxContentExtensionInterface[]
     */
    protected $extensions = [];

    /**
     * @param AjaxContentExtensionInterface $extension
     */
    public function addExtension(AjaxContentExtensionInterface $extension)
    {
        $this->extensions[$extension->getName()] = $extension;
    }

    /**
     * @param $name
     * @return AjaxContentExtensionInterface|null
     */
    public function getExtension($name)
    {
        return isset($this->extensions[$name]) ? $this->extensions[$name] : null;
    }

    /**
     * @return AjaxContentExtensionInterface[]
     */
    public function getExtensions()
    {
        return $this->extensions;
    }
}