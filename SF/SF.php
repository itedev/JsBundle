<?php

namespace ITE\JsBundle\SF;

use ITE\Common\CdnJs\ApiWrapper;
use ITE\Common\CdnJs\Resource\Reference;
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
     * @var SFExtensionInterface[] $extensions
     */
    protected $extensions = array();

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
    public function getStylesheets()
    {
        $inputs = array();
        foreach ($this->extensions as $extension) {
            /** @var $extension SFExtensionInterface */
            $inputs = array_merge($inputs, $extension->getStylesheets());
        }

        return $inputs;
    }

    /**
     * @return array
     */
    public function getJavascripts()
    {
        $inputs = array('@ITEJsBundle/Resources/public/js/sf.js');
        foreach ($this->extensions as $extension) {
            /** @var $extension SFExtensionInterface */
            $inputs = array_merge($inputs, $extension->getJavascripts());
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
            $dump .= $extension->getInlineJavascripts();
        }

        $dump = '<script>/*<![CDATA[*/ ' . $dump . ' /*]]>*/</script>';

        return $dump;
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onAjaxRequest(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();

        $ajaxRequestEvent = new AjaxRequestEvent($event);

        foreach ($this->extensions as $name => $extension) {
            /** @var $extension SFExtensionInterface */
            $extension->onAjaxRequest($ajaxRequestEvent);
        }

        if ($ajaxRequestEvent->hasAjaxData()) {
            $request->attributes->set('_sf_ajax_data', $ajaxRequestEvent->getAjaxData());
        }

        if ($ajaxRequestEvent->isResponseOverridden()) {
            $request->attributes->set('_sf_response_overridden', $ajaxRequestEvent->getResponse());
        }
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onAjaxResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        $request = $event->getRequest();

        $response->headers->set('X-SF-Route', $request->attributes->get('_route'));

        foreach ($this->extensions as $extension) {
            /** @var $extension SFExtensionInterface */
            $extension->onAjaxResponse($event);
        }

        if (null !== ($ajaxData = $request->attributes->get('_sf_ajax_data'))
                || $request->attributes->has('_sf_response_overridden')
        ) {
            $responseInjector = new ResponseInjector();
            $responseInjector->injectAjaxData($request, $response, $ajaxData !== null ? $ajaxData : []);
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
            'kernel.environment' => $this->container->getParameter('kernel.environment'),
            'kernel.debug' => $this->container->getParameter('kernel.debug'),
            'locale' => $this->container->getParameter('locale'),
            'route' => $this->container->get('request_stack')->getMasterRequest()->attributes->get('_route'),
        ));
    }

}