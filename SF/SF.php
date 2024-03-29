<?php

namespace ITE\JsBundle\SF;

use ITE\JsBundle\EventListener\Event\AjaxRequestEvent;
use ITE\JsBundle\EventListener\Event\AjaxResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Class SF
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class SF implements SFInterface
{
    /**
     * @var ParameterBag
     */
    public $parameters;

    /**
     * @var AjaxParameterBag
     */
    public $ajaxParameters;

    /**
     * @var AjaxDataBag
     */
    protected $ajaxDataBag;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var SFExtensionInterface[] $extensions
     */
    protected $extensions = [];

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->parameters = new ParameterBag();
        $this->ajaxParameters = new AjaxParameterBag();
        $this->ajaxDataBag = new AjaxDataBag();
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
     * @throws \InvalidArgumentException
     */
    public function getExtension($name)
    {
        if (!$this->hasExtension($name)) {
            throw new \InvalidArgumentException();
        }

        return $this->extensions[$name];
    }

    /**
     * Get parameters
     *
     * @return ParameterBag
     */
    public function getParameters(): ParameterBag
    {
        return $this->parameters;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setParameter($name, $value)
    {
        $this->parameters->set($name, $value);

        return $this;
    }

    /**
     * @param string $name
     * @param mixed|null $defaultValue
     * @return mixed
     */
    public function getParameter($name, $defaultValue = null)
    {
        return $this->parameters->get($name, $defaultValue);
    }

    /**
     * {@inheritdoc}
     */
    public function getStylesheets()
    {
        $inputs = [];
        foreach ($this->extensions as $extension) {
            /** @var $extension SFExtensionInterface */
            $inputs = array_merge($inputs, $extension->getStylesheets());
        }

        return $inputs;
    }

    /**
     * {@inheritdoc}
     */
    public function getJavascripts()
    {
        $inputs = ['@ITEJsBundle/Resources/public/js/sf.js'];
        foreach ($this->extensions as $extension) {
            /** @var $extension SFExtensionInterface */
            $inputs = array_merge($inputs, $extension->getJavascripts());
        }

        return $inputs;
    }

    /**
     * {@inheritdoc}
     */
    public function getCdnStylesheets($debug)
    {
        $stylesheets = [];
        foreach ($this->extensions as $extension) {
            /** @var $extension SFExtensionInterface */
            $stylesheets = array_merge($stylesheets, $extension->getCdnStylesheets($debug));
        }

        return $stylesheets;
    }

    /**
     * {@inheritdoc}
     */
    public function getCdnJavascripts($debug)
    {
        $javascripts = [];
        foreach ($this->extensions as $extension) {
            /** @var $extension SFExtensionInterface */
            $javascripts = array_merge($javascripts, $extension->getCdnJavascripts($debug));
        }

        return $javascripts;
    }

    public function dump(bool $defer = false, bool $async = false): string
    {
        $this->initializeParameters();

        $dump = '/*<![CDATA[*/ ';
        $dump .= 'SF.parameters.add(' . json_encode($this->parameters->all()) . ');';

        foreach ($this->extensions as $extension) {
            /** @var $extension SFExtensionInterface */
            $dump .= $extension->dump();
        }

        $dump .= ' /*]]>*/';

        // $dump = base64_encode($dump);

        $dump = '<script' . ($defer ? ' defer' : '') . ($async ? ' async' : '') . ' id="_sf_javascripts" type="application/json">' . $dump . '</script>';

        return $dump;
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onAjaxRequest(GetResponseForControllerResultEvent $event)
    {
        $ajaxRequestEvent = new AjaxRequestEvent($event, $this->ajaxDataBag);
        foreach ($this->extensions as $name => $extension) {
            /** @var $extension SFExtensionInterface */
            $extension->onAjaxRequest($ajaxRequestEvent);
        }

        $request = $event->getRequest();
        if ($ajaxRequestEvent->hasContent()) {
            $request->attributes->set('_sf_content', $ajaxRequestEvent->getContent());
        }

        if ($ajaxRequestEvent->isParentEventPropagationStopped()) {
            $event->setResponse($ajaxRequestEvent->getResponse() ?: new Response());
            $event->stopPropagation();
        }
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onAjaxResponse(FilterResponseEvent $event)
    {
        $ajaxResponseEvent = new AjaxResponseEvent($event, $this->ajaxDataBag);
        foreach ($this->extensions as $extension) {
            /** @var $extension SFExtensionInterface */
            $extension->onAjaxResponse($ajaxResponseEvent);
        }

        $response = $event->getResponse();

        if (null !== $originalResponseContent = $ajaxResponseEvent->getAjaxDataBag()->getOriginalResponse()) {
            $response->setContent($originalResponseContent);
        }

        $request = $event->getRequest();

        $this->ajaxDataBag
            ->addHeaderData('parameters', $this->parameters->all())
            ->addHeaderData('route', $request->attributes->get('_route'))
            ->addHeaderData('ajax_parameters', $this->ajaxParameters->getHeaderData())
        ;
        if ($this->ajaxParameters->bodyDataCount()) {
            $this->ajaxDataBag->addBodyData('ajax_parameters', $this->ajaxParameters->getBodyData(), true);
        }

        if (in_array($response->getStatusCode(), [301, 302, 303, 305, 307])) {
            $this->ajaxDataBag->addHeaderData('redirect', $response->headers->get('Location'));
            $response->headers->remove('Location');
            $response->setStatusCode(200);
        }

        if (null !== $content = $request->attributes->get('_sf_content')) {
            $response->setContent($content);
        }

        $responseInjector = new ResponseInjector();
        // add ajax header data
        $responseInjector->injectHeaderData($response, $this->ajaxDataBag->getHeaderData());
        // add ajax body data
        if ($this->ajaxDataBag->bodyDataCount()) {
            $responseInjector->injectBodyData($request, $response, $this->ajaxDataBag->getBodyData());
        }
    }

    public function __sleep()
    {
        return [];
    }

    /**
     *
     */
    protected function initializeParameters()
    {
        $this->parameters->add([
            'kernel.environment' => $this->container->getParameter('kernel.environment'),
            'kernel.debug' => $this->container->getParameter('kernel.debug'),
            'locale' => $this->container->getParameter('locale'),
            'route' => $this->container->get('request_stack')->getMasterRequest()->attributes->get('_route'),
        ]);
    }
}
