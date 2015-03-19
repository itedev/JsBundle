<?php
/**
 * This file is created by sam0delkin (t.samodelkin@gmail.com).
 * IT-Excellence (http://itedev.com)
 * Date: 19.03.2015
 * Time: 13:16
 */

namespace ITE\JsBundle\AjaxContent\Extension;


use ITE\JsBundle\AjaxBlock\AjaxBlockRenderer;
use ITE\JsBundle\AjaxContent\AjaxContentExtensionInterface;
use ITE\JsBundle\Annotation\AjaxBlock;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Templating\TemplateReference;

/**
 * Class AjaxBlockExtension
 *
 * @package ITE\JsBundle\AjaxContent\Extension
 */
class AjaxBlockExtension implements AjaxContentExtensionInterface
{
    /**
     * @var AjaxBlockRenderer
     */
    protected $renderer;

    /**
     * @var array
     */
    protected $defaults;


    /**
     * @param AjaxBlockRenderer $renderer
     * @param array             $defaults
     */
    public function __construct(AjaxBlockRenderer $renderer, array $defaults)
    {
        $this->renderer = $renderer;
        $this->defaults = $defaults;
    }

    /**
     * @return array
     */
    public function getDataForAjaxResponse(GetResponseForControllerResultEvent $event)
    {
        $request  = $event->getRequest();

        $configuration = $request->attributes->get('_ajax_block');

        $data = [];

        /** @var AjaxBlock $annotation */
        foreach ($configuration as $annotation) {
            $data[$annotation->getSelector()] = [
              'block_data'     => $this->renderer->render(
                $this->getTemplate($request, $annotation),
                $annotation->getBlockName(),
                $event->getControllerResult()
              ),
              'show_animation' => $annotation->getShowAnimation(
              ) === null ? $this->defaults['show_animation'] : $annotation->getShowAnimation(),
              'length'         => $annotation->getShowLength(
              ) === null ? $this->defaults['show_length'] : $annotation->getShowLength(),
            ];
        }

        return $data;
    }

    /**
     * @param Request   $request
     * @param AjaxBlock $configuration
     * @return string
     */
    protected function getTemplate(Request $request, AjaxBlock $configuration)
    {
        if ($configuration->getTemplate()) {
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

    /**
     * @return string
     */
    public function getJavascriptResource()
    {
        return '@ITEJsBundle/Resources/public/js/content/ajax_block.js';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'blocks';
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request)
    {
        return $request->attributes->has('_ajax_block');
    }

}