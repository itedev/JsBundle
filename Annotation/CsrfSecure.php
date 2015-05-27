<?php

namespace ITE\JsBundle\Annotation;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationAnnotation;

/**
 * @Annotation
 * @Target("METHOD")
 */
class CsrfSecure extends ConfigurationAnnotation
{
    /**
     * @var string $tokenId
     */
    protected $tokenId = 'link';

    /**
     * Get tokenId
     *
     * @return string
     */
    public function getTokenId()
    {
        return $this->tokenId;
    }

    /**
     * Set tokenId
     *
     * @param string $tokenId
     * @return CsrfSecure
     */
    public function setTokenId($tokenId)
    {
        $this->tokenId = $tokenId;

        return $this;
    }

    /**
     * @param $tokenId
     */
    public function setValue($tokenId)
    {
        $this->setTokenId($tokenId);
    }

    /**
     * {@inheritdoc}
     */
    public function getAliasName()
    {
        return 'csrf_secure';
    }

    /**
     * {@inheritdoc}
     */
    public function allowArray()
    {
        return false;
    }

}