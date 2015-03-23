<?php
/**
 * This file is created by sam0delkin (t.samodelkin@gmail.com).
 * IT-Excellence (http://itedev.com)
 * Date: 12.03.2015
 * Time: 18:17
 */

namespace ITE\JsBundle\SF\Extension\AjaxBlock;

/**
 * Class AjaxBlockRenderer
 * @package ITE\JsBundle\SF\Extension\AjaxBlock
 */
class AjaxBlockRenderer
{
    /**
     * @var array
     */
    protected $storage = array();

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Renders ajax block from the base template with given context.
     *
     * @param string $baseTemplateName
     * @param string $ajaxBlockName
     * @param array  $context
     *
     * @return string
     */
    public function render($baseTemplateName, $ajaxBlockName, $context = array())
    {
        $blockHash = $this->generateBlockHash($baseTemplateName, $ajaxBlockName);

        if (isset($this->storage[$blockHash])) {
            return $this->storage[$blockHash];
        }

        AjaxBlockStorage::clearStorage();
        $this->twig->render($baseTemplateName, $context);

        foreach (AjaxBlockStorage::getStorage() as $blockName => $blockContent) {
            $hash                 = $this->generateBlockHash($baseTemplateName, $blockName);
            $this->storage[$hash] = $blockContent;
        }

        if (!isset($this->storage[$blockHash])) {
            throw new \InvalidArgumentException(
              sprintf('Ajax block "%s" was not found in the template "%s".',
              $ajaxBlockName,
              $baseTemplateName
            ));
        }

        return $this->storage[$blockHash];
    }

    /**
     * Generates hash for identify needed ajax block.
     *
     * @param string $baseTemplateName
     * @param string $ajaxBlockName
     *
     * @return string
     */
    protected function generateBlockHash($baseTemplateName, $ajaxBlockName)
    {
        $hashString = $baseTemplateName . $ajaxBlockName;

        return md5($hashString);
    }
}