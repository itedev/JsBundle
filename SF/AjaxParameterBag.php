<?php

namespace ITE\JsBundle\SF;

/**
 * Class AjaxParameterBag
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class AjaxParameterBag
{
    /**
     * @var array
     */
    private $headerData = [];

    /**
     * @var array
     */
    private $bodyData = [];

    public function set(string $name, $value, bool $body = false): AjaxParameterBag
    {
        if ($body) {
            $this->addBodyData($name, $value);
        } else {
            $this->addHeaderData($name, $value);
        }

        return $this;
    }

    public function hasHeaderData(string $name): bool
    {
        return array_key_exists($name, $this->headerData);
    }

    public function addHeaderData(string $name, $value): AjaxParameterBag
    {
        if ($this->hasData($name)) {
            throw new \RuntimeException(sprintf('Key "%s" already exists', $name));
        }

        $this->headerData[$name] = $value;

        return $this;
    }

    public function getHeaderData(): array
    {
        return $this->headerData;
    }

    public function headerDataCount(): int
    {
        return count($this->headerData);
    }

    public function hasBodyData(string $name): bool
    {
        return array_key_exists($name, $this->bodyData);
    }

    public function addBodyData(string $name, $value): AjaxParameterBag
    {
        if ($this->hasData($name)) {
            throw new \RuntimeException(sprintf('Key "%s" already exists', $name));
        }

        $this->bodyData[$name] = $value;

        return $this;
    }

    public function getBodyData(): array
    {
        return $this->bodyData;
    }

    public function bodyDataCount(): int
    {
        return count($this->bodyData);
    }

    public function hasData(string $name): bool
    {
        return $this->hasHeaderData($name) || $this->hasBodyData($name);
    }
}
