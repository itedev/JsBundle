<?php

namespace ITE\JsBundle\EventListener\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class FilterAssetEvent
 * @package ITE\JsBundle\EventListener\Event
 */
class FilterAssetEvent extends Event
{
    protected $filename;
    protected $tag;
    protected $inputs;

    /**
     * @param $filename
     * @param $tag
     * @param $inputs
     */
    public function __construct($filename, $tag, $inputs)
    {
        $this->filename = $filename;
        $this->tag = $tag;
        $this->inputs = $inputs;
    }

    /**
     * Get filename
     *
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Get inputs
     *
     * @return mixed
     */
    public function getInputs()
    {
        return $this->inputs;
    }

    /**
     * Set inputs
     *
     * @param mixed $inputs
     * @return FilterAssetEvent
     */
    public function setInputs($inputs)
    {
        $this->inputs = $inputs;

        return $this;
    }

    /**
     * Get tag
     *
     * @return mixed
     */
    public function getTag()
    {
        return $this->tag;
    }

} 