<?php

namespace ITE\JsBundle\SF;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class ResponseInjector
 * @package ITE\JsBundle\SF
 */
class ResponseInjector
{
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @param Request $request
     * @param Response $response
     * @param array $data
     */
    public function injectAjaxData(Request $request, Response $response, array $data)
    {
        $requestFormat = 'html' === $request->getRequestFormat()
            ? 'json'
            : $request->getRequestFormat();

        if ('html' !== $request->getRequestFormat()) {
            $originalData = $this->getSerializer()->decode($response->getContent(), $requestFormat);
        } else {
            $originalData = $response->getContent();
        }

        $prefixedData = [];
        foreach ($data as $name => $value) {
            $prefixedData['_sf_' . $name] = $value;
        }

        $extendedData = array_merge(['_sf_data' => $originalData], $prefixedData);
        $content      = $this->getSerializer()->encode($extendedData, $requestFormat);

        $response->setContent($content);
        $response->headers->add(['X-SF-Ajax-Content' => 1]);
    }

    /**
     * @return Serializer|SerializerInterface
     */
    protected function getSerializer()
    {
        if ($this->serializer) {
            return $this->serializer;
        }

        return $this->serializer = new Serializer([], [new JsonEncoder(), new XmlEncoder()]);
    }

}