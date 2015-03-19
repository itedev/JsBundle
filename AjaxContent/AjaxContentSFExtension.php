<?php
/**
 * This file is created by sam0delkin (t.samodelkin@gmail.com).
 * IT-Excellence (http://itedev.com)
 * Date: 19.03.2015
 * Time: 13:25
 */

namespace ITE\JsBundle\AjaxContent;


use ITE\JsBundle\SF\SFExtension;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class AjaxContentSFExtension
 *
 * @package ITE\JsBundle\AjaxContent
 */
class AjaxContentSFExtension extends SFExtension
{
    /**
     * @var ExtensionManager
     */
    protected $extensionManager;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @param ExtensionManager    $extensionManager
     */
    public function __construct(ExtensionManager $extensionManager)
    {
        $this->extensionManager = $extensionManager;
    }

    public function addJavascripts()
    {
        $js = [];
        foreach ($this->extensionManager->getAllExtensions() as $extension) {
            if($jsPath = $extension->getJavascriptResource()) {
                $js []= $jsPath;
            }
        }

        return $js;
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onAjaxRequest(GetResponseForControllerResultEvent $event)
    {
        $contentData = [];

        foreach ($this->extensionManager->getAllExtensions() as $extension) {
            if ($extension->supports($event->getRequest())) {
                $contentData[$extension->getName()] = $extension->getDataForAjaxResponse($event);
            }
        }

        if (!empty($contentData)) {
            $event->getRequest()->attributes->set('_ajax_response', $contentData);
        }
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onAjaxResponse(FilterResponseEvent $event)
    {
        if ($ajaxResponse = $event->getRequest()->attributes->get('_ajax_response')) {
            $this->injectData($event->getRequest(), $event->getResponse(), $ajaxResponse);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array   $data
     */
    protected function injectData(Request $request, Response $response, array $data)
    {
        $requestFormat = $request->getRequestFormat() === 'html' ? 'json' : $request->getRequestFormat();

        if ($response && $request->getRequestFormat() !== 'html') {
            $originalData = $this->getSerializer()->decode($response->getContent(), $requestFormat);
        } else {
            $originalData = $response->getContent();
        }
        $extendedData = array_merge(['data' => $originalData], $data);
        $content      = $this->getSerializer()->encode($extendedData, $requestFormat);
        $response->setContent($content);
        $response->headers->set('X-SF-Ajax-Content', 1);
    }

    /**
     * @return Serializer|SerializerInterface
     */
    protected function getSerializer()
    {
        if($this->serializer) {
            return $this->serializer;
        }

        return $this->serializer = new Serializer([], [new JsonEncoder(), new XmlEncoder()]);
    }

}