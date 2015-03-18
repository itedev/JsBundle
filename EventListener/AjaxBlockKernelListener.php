<?php
/**
 * This file is created by sam0delkin (t.samodelkin@gmail.com).
 * IT-Excellence (http://itedev.com)
 * Date: 13.03.2015
 * Time: 11:07
 */

namespace ITE\JsBundle\EventListener;

use ITE\JsBundle\AjaxBlock\AjaxBlockRenderer;
use ITE\JsBundle\Annotation\AjaxBlock;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class AjaxBlockKernelListener
 *
 * @package ITE\JsBundle\EventListener
 */
class AjaxBlockKernelListener
{
    /**
     * @var AjaxBlockRenderer
     */
    protected $renderer;

    public function __construct(AjaxBlockRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request  = $event->getRequest();
        $response = $event->getResponse();

        if (
          !$event->isMasterRequest()
          || !($configuration = $request->attributes->get('_ajax_block'))
          || !$request->isXmlHttpRequest()
          || $request->getRequestFormat() !== 'json'
          || !$request->headers->has('X-SF-Ajax')
        ) {
            return;
        }

        $originalData = json_decode($response->getContent(), true);

        $data = array(
          'data'   => $originalData,
          'blocks' => array()
        );

        /** @var AjaxBlock $annotation */
        foreach ($configuration as $annotation) {
            $data['blocks'][$annotation->getSelector()] = [
              'block_data'     => $this->renderer->render(
                $this->getTemplate($request, $annotation),
                $annotation->getBlockName(),
                $originalData
              ),
              'show_animation' => $annotation->getShowAnimation(),
              'length'         => $annotation->getShowLength(),
            ];
        }

        $response->headers->add(array('X-SF-Ajax-Blocks' => count($configuration)));
        $response->setContent(json_encode($data));
        $response->headers->set('Content-Type', 'application/ite_content');
    }

    /**
     * @param Request   $request
     * @param AjaxBlock $configuration
     * @return string
     */
    protected function getTemplate(Request $request, AjaxBlock $configuration)
    {
        if($configuration->getTemplate()) {
            return $configuration->getTemplate();
        }

        /** @var TemplateReference $template */
        $template = $request->attributes->get('_template');
        if (!$template) {
            throw new \InvalidArgumentException('You should set template for render ajax_block.');
        }
        $originalFormat = $template->get('format');
        $template->set('format', 'html');
        $templateName = $template->getPath();
        $template->set('format', $originalFormat);

        return $templateName;
    }
}