<?php

namespace ITE\JsBundle\SF;

use ITE\JsBundle\Utils\Inflector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class ResponseInjector
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ResponseInjector
{
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @param Response $response
     * @param array $data
     */
    public function injectHeaderData(Response $response, array $data)
    {
        foreach ($data as $name => $value) {
            $response->headers->set('X-SF-' . Inflector::headerize($name), json_encode($value));
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param mixed $data
     */
    public function injectBodyData(Request $request, Response $response, array $data)
    {
        $requestFormat = 'html' === $request->getRequestFormat()
            ? 'json'
            : $request->getRequestFormat();

        /** @var Response $responseOverridden */
        if ('html' !== $request->getRequestFormat()) {
            $originalData = $this->getSerializer()->decode($response->getContent(), $requestFormat);
        } else {
            $originalData = $response->getContent();
        }

        $extraData = [];
        foreach ($data as $name => $value) {
            $extraData['_sf_' . $name] = $value;
        }

        $extendedData = array_merge(['_sf_data' => $originalData], $extraData);
        $content      = $this->getSerializer()->encode($extendedData, $requestFormat);
        $response->setContent($content);
        $response->headers->add(['X-SF-Body-Data' => 1]);
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