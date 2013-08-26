<?php

namespace ITE\JsBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class SF implements SFInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ParameterBagInterface
     */
    protected $parameterBag;

    /**
     * @var array $flashes
     */
    protected $flashes = array();

    /**
     * @var array $extensions
     */
    protected $extensions = array();

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->parameterBag = new ParameterBag();

        $this->initializeParameters();
    }

    /**
     * @return ParameterBagInterface
     */
    public function getParameterBag()
    {
        return $this->parameterBag;
    }

    /**
     * @param $extensionName
     * @return bool
     */
    public function hasExtension($extensionName)
    {
        return array_key_exists($extensionName, $this->extensions);
    }

    /**
     * @param $extensionName
     * @param SFExtensionInterface $extension
     * @return SFInterface
     */
    public function addExtension($extensionName, SFExtensionInterface $extension)
    {
        if (!$this->hasExtension($extensionName)) {
            $this->extensions[$extensionName] = $extension;
        }
        return $this;
    }

    /**
     * @param $extensionName
     * @return SFExtensionInterface
     * @throws InvalidArgumentException
     */
    public function getExtension($extensionName)
    {
        if (!$this->hasExtension($extensionName)) {
            throw new InvalidArgumentException();
        }
        return $this->extensions[$extensionName];
    }

    /**
     * @return string
     */
    public function dump()
    {
        $dump = '';

        $dump .= 'SF.parameters.add(' . json_encode($this->parameterBag->all()) . ');';

        foreach ($this->extensions as $extension) {
            /** @var $extension SFExtensionInterface */
            $dump .= $extension->dump();
        }

        return '<script>/*<![CDATA[*/ (function($){$(document).ready(function(){' . $dump . '});})(jQuery); /*]]>*/</script>';
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $this->collectFlashes();

        foreach ($this->extensions as $extension) {
            /** @var $extension SFExtensionInterface */
            $extension->onKernelView($event);
        }
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();

        if (count($this->flashes)) {
            $response->headers->set('X-SF-Flashes', json_encode($this->flashes));
        }

        foreach ($this->extensions as $extension) {
            /** @var $extension SFExtensionInterface */
            $extension->onKernelResponse($event);
        }
    }

    /**
     *
     */
    protected function initializeParameters()
    {
        $this->parameterBag->add(array(
           'environment' => $this->container->getParameter('kernel.environment'),
           'debug' => $this->container->getParameter('kernel.debug'),
           'locale' => $this->container->getParameter('locale'),
           'flashes_selector' => $this->container->getParameter('ite_js.flashes_selector'),
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