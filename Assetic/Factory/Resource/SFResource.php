<?php

namespace ITE\JsBundle\Assetic\Factory\Resource;

use Assetic\Factory\Resource\ResourceInterface;
use ITE\JsBundle\SF\SFInterface;

/**
 * Class SFResource
 * @package ITE\JsBundle\Assetic\Factory\Resource
 */
class SFResource implements ResourceInterface
{
    /**
     * @var SFInterface $sf
     */
    protected $sf;

    /**
     * @param SFInterface $sf
     */
    public function __construct(SFInterface $sf)
    {
        $this->sf = $sf;
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh($timestamp)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return array(
            'sf_stylesheets' => array(
                $this->sf->getStylesheets(),
                array(),
                array()
            ),
            'sf_javascripts' => array(
                $this->sf->getJavascripts(),
                array(),
                array()
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return 'sf';
    }
}