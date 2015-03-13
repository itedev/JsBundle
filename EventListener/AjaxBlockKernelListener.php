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
        ) {
            return;
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
        $originalData = json_decode($response->getContent());

        $data = array(
          'data'   => $originalData,
          'blocks' => array()
        );

        /** @var AjaxBlock $annotation */
        foreach ($configuration as $annotation) {
            $data['blocks'][$annotation->getSelector()] = $this->renderer->render(
              $templateName,
              $annotation->getBlockName(),
              $originalData
            );
        }

        $response->setContent(json_encode($data));
    }
}