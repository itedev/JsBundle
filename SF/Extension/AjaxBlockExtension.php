<?php
/**
 * This file is created by sam0delkin (t.samodelkin@gmail.com).
 * IT-Excellence (http://itedev.com)
 * Date: 23.03.2015
 * Time: 14:45
 */

namespace ITE\JsBundle\SF\Extension;

use ITE\JsBundle\Annotation\AjaxBlock;
use ITE\JsBundle\EventListener\Event\AjaxRequestEvent;
use ITE\JsBundle\SF\Extension\AjaxBlock\AjaxBlockRenderer;
use ITE\JsBundle\SF\SFExtension;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Templating\TemplateReference;

/**
 * Class AjaxBlockExtension
 *
 * @package ITE\JsBundle\SF\Extension
 */
class AjaxBlockExtension extends SFExtension
{
    /**
     * @var AjaxBlockRenderer
     */
    protected $renderer;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param AjaxBlockRenderer $renderer
     * @param array             $options
     */
    public function __construct(AjaxBlockRenderer $renderer, array $options)
    {
        $this->renderer = $renderer;
        $this->options  = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getJavascripts()
    {
        return ['@ITEJsBundle/Resources/public/js/extension/sf.ajax_block.js'];
    }

    /**
     * {@inheritdoc}
     */
    public function getAjaxContent(AjaxRequestEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->attributes->has('_ajax_block')) {
            return [];
        }

        $configuration = $request->attributes->get('_ajax_block');

        $data = [];
        foreach ($configuration as $annotation) {
            /** @var AjaxBlock $annotation */
            $data[$annotation->getSelector()] = [
                'content' => $this->renderer->render(
                    $this->getTemplate($request, $annotation),
                    $annotation->getBlockName(),
                    $event->getControllerResult()
                ),
                'show_animation' => [
                    'type' => null === $annotation->getShowAnimation()
                        ? $this->options['show_animation']['type']
                        : $annotation->getShowAnimation(),
                    'length' => null === $annotation->getShowLength()
                        ? $this->options['show_animation']['length']
                        : $annotation->getShowLength()
                ],
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

}