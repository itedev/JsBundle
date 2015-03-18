<?php
/**
 * This file is created by sam0delkin (t.samodelkin@gmail.com).
 * IT-Excellence (http://itedev.com)
 * Date: 13.03.2015
 * Time: 10:36
 */

namespace ITE\JsBundle\Annotation;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationAnnotation;

/**
 * Class AjaxBlock
 *
 * @package ITE\JsBundle\Annotation
 * @Annotation()
 */
class AjaxBlock extends ConfigurationAnnotation
{
    /**
     * @var string
     */
    protected $template;

    /**
     * @var string
     */
    protected $blockName;

    /**
     * @var string
     */
    protected $selector;

    /**
     * @var string
     */
    protected $showAnimation = 'show';

    /**
     * @var int
     */
    protected $showLength = 0;

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }


    /**
     * @return string
     */
    public function getBlockName()
    {
        return $this->blockName;
    }

    /**
     * @param string $blockName
     */
    public function setBlockName($blockName)
    {
        $this->blockName = $blockName;
    }

    /**
     * @return string
     */
    public function getSelector()
    {
        return $this->selector;
    }

    /**
     * @param string $selector
     */
    public function setSelector($selector)
    {
        $this->selector = $selector;
    }

    /**
     * @return string
     */
    public function getShowAnimation()
    {
        return $this->showAnimation;
    }

    /**
     * @param string $showAnimation
     */
    public function setShowAnimation($showAnimation)
    {
        $this->showAnimation = $showAnimation;
    }

    /**
     * @return int
     */
    public function getShowLength()
    {
        return $this->showLength;
    }

    /**
     * @param int $showLength
     */
    public function setShowLength($showLength)
    {
        $this->showLength = $showLength;
    }


    public function getAliasName()
    {
        return 'ajax_block';
    }

    public function allowArray()
    {
        return true;
    }

}