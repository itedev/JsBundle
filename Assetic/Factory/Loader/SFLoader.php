<?php

namespace ITE\JsBundle\Assetic\Factory\Loader;

use Assetic\Factory\Loader\FormulaLoaderInterface;
use Assetic\Factory\Resource\ResourceInterface;
use ITE\JsBundle\Assetic\Factory\Resource\SFResource;

/**
 * Class SFLoader
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
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