<?php

namespace ITE\JsBundle\Assetic\Factory\Loader;

use Assetic\Factory\Loader\FormulaLoaderInterface;
use Assetic\Factory\Resource\ResourceInterface;
use ITE\JsBundle\Assetic\Factory\Resource\SFResource;

/**
 * Class SFLoader
 * @package ITE\JsBundle\Assetic\Factory\Loader
 */
class SFLoader implements FormulaLoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ResourceInterface $resource)
    {
        return $resource instanceof SFResource ? $resource->getContent() : array();
    }
}