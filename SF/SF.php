<?php

namespace ITE\JsBundle\SF;

use ITE\JsBundle\EventListener\Event\AjaxRequestEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class SF
 * @package ITE\JsBundle\SF
 */
class SF implements SFInterface
{
    /**
     * @var ParameterBagInterface
     */
    public $parameters;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array $flashes
     */
    protected $flashes = array();

    /**
     * @var array $extensions
     */
    protected $extensions = array();

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->parameters = new ParameterBag();
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasExtension($name)
    {
        return array_key_exists($name, $this->extensions);
    }

    /**
     * @param $name
     * @param SFExtensionInterface $extension
     * @return SFInterface
     */
    public function addExtension($name, SFExtensionInterface $extension)
    {
        if (!$this->hasExtension($name)) {
            $this->extensions[$name] = $extension;
        }
        return $this;
    }

    /**
     * @param $name
     * @return SFExtensionInterface
     * @throws InvalidArgumentException
     */
    public function getExtension($name)
    {
        if (!$this->hasExtension($name)) {
            throw new InvalidArgumentException();
        }

        return $this->extensions[$name];
    }

    /**
     * @return array
     */
    public function addStylesheets()
    {
        $inputs = array();
        foreach ($this->extensions as $extension) {
            /** @var $extension SFExtensionInterface */
            $inputs = array_merge($inputs, $extension->addStylesheets());
        }

        return $inputs;
    }

    /**
     * @return array
     */
    public function addJavascripts()
    {
        $inputs = array('@ITEJsBundle/Resources/public/js/sf.js');
        foreach ($this->extensions as $extension) {
            /** @var $extension SFExtensionInterface */
            $inputs = array_merge($inputs, $extension->addJavascripts());
        }

        return $inputs;
    }

    /**
     * @return string
     */
    public function dump()
    {
        $this->initializeParameters();

        $dump = '';
        $dump .= 'SF.parameters.add(' . json_encode($this->parameters->all()) . ');';

        foreach ($this->extensions as $extension) {
            /** @var $extension SFExtensionInterface */
            $dump .= $extension->dump();
        }

        return '<script>/*<![CDATA[*/ ' . $dump . ' /*]]>*/</script>';
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onAjaxRequest(GetResponseForControllerResultEvent $event)
    {
        $this->collectFlashes();

        $ajaxRequestEvent = new AjaxRequestEvent($event);
        $ajaxContent      = [];

        foreach ($this->extensions as $name => $extension) {
            /** @var $extension SFExtensionInterface */
            $extension->onAjaxRequest($event);
            if (count($extensionContent = $extension->getAjaxContent($ajaxRequestEvent))) {
                $ajaxContent['_sf_' . $name] = $extensionContent;
            }
        }

        if (count($ajaxContent)) {
            $event->getRequest()->attributes->set('_sf_ajax_content', $ajaxContent);
        }
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $data
     */
    protected function injectData(Request $request, Response $response, array $data)
    {
        $requestFormat = $request->getRequestFormat() === 'html' ? 'json' : $request->getRequestFormat();

        if ($response && $request->getRequestFormat() !== 'html') {
            $originalData = $this->getSerializer()->decode($response->getContent(), $requestFormat);
        } else {
            $originalData = $response->getContent();
        }
        $extendedData = array_merge(['_sf_data' => $originalData], $data);
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

    /**
     * @param FilterResponseEvent $event
     */
    public function onAjaxResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();

        if (count($this->flashes)) {
            $response->headers->set('X-SF-Flashes', json_encode($this->flashes));
        }

        if ($this->parameters->count()) {
            $response->headers->set('X-SF-Parameters', json_encode($this->parameters->all()));
        }

        $response->headers->set('X-SF-Route', $this->container->get('request')->get('_route'));

        foreach ($this->extensions as $extension) {
            /** @var $extension SFExtensionInterface */
            $extension->onAjaxResponse($event);
        }

        if (null !== $ajaxResponse = $event->getRequest()->attributes->get('_sf_ajax_content')) {
            $this->injectData($event->getRequest(), $event->getResponse(), $ajaxResponse);
        }
    }

    public function __sleep()
    {
        return array();
    }

    /**
     *
     */
    protected function initializeParameters()
    {
        $this->parameters->add(array(
            'environment' => $this->container->getParameter('kernel.environment'),
            'debug' => $this->container->getParameter('kernel.debug'),
            'locale' => $this->container->getParameter('locale'),
            'route' => $this->container->get('request')->get('_route'),
        ));
    }

    /**
     *
     */
    protected function collectFlashes()
    {
        /** @var $session SessionInterface */
        $session = $this->container->get('session');

        if (count($session->getFlashBag()->peekAll())) {
            $this->flashes = $session->getFlashBag()->all();
        }
    }
}