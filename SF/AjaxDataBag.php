<?php

namespace ITE\JsBundle\SF;

use ITE\JsBundle\Utils\Inflector;

/**
 * Class AjaxDataBag
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class AjaxDataBag
{
    /**
     * @var string $originalResponse
     */
    private $originalResponse;

    /**
     * @var array
     */
    private $headerData = [];

    /**
     * @var array
     */
    private $bodyData = [];

    /**
     * @param string $name
     * @return bool
     */
    public function hasHeaderData($name)
    {
        return array_key_exists($name, $this->headerData);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function addHeaderData($name, $value)
    {
        $name = Inflector::underscore($name);
        if ($this->hasData($name)) {
            throw new \RuntimeException(sprintf('Key "%s" already exists'));
        }

        $this->headerData[$name] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getHeaderData()
    {
        return $this->headerData;
    }

    /**
     * @return int
     */
    public function headerDataCount()
    {
        return count($this->headerData);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasBodyData($name)
    {
        return array_key_exists($name, $this->bodyData);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function addBodyData($name, $value)
    {
        $name = Inflector::underscore($name);
        if ($this->hasData($name)) {
            throw new \RuntimeException(sprintf('Key "%s" already exists'));
        }

        $this->bodyData[$name] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getBodyData()
    {
        return $this->bodyData;
    }

    /**
     * @return int
     */
    public function bodyDataCount()
    {
        return count($this->bodyData);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasData($name)
    {
        return $this->hasHeaderData($name) || $this->hasBodyData($name);
    }

    /**
     * Get originalResponse
     *
     * @return string
     */
    public function getOriginalResponse()
    {
        return $this->originalResponse;
    }

    /**
     * Set originalResponse
     *
     * @param string $originalResponse
     * @return AjaxDataBag
     */
    public function setOriginalResponse($originalResponse)
    {
        $this->originalResponse = $originalResponse;

        return $this;
    }

    /**
     * @param AjaxDataBag $ajaxDataBag
     * @return $this
     */
    public function merge(AjaxDataBag $ajaxDataBag)
    {
        foreach ($ajaxDataBag->getHeaderData() as $name => $value) {
            $this->addHeaderData($name, $value);
        }
        foreach ($ajaxDataBag->getBodyData() as $name => $value) {
            $this->addBodyData($name, $value);
        }

        $this->setOriginalResponse($ajaxDataBag->getOriginalResponse());

        return $this;
    }
}
