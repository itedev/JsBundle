<?php

namespace ITE\JsBundle\Twig\Extension;

use ITE\JsBundle\SF\SFInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig_Extension;

/**
 * Class LinkExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class LinkExtension extends Twig_Extension
{
    /**
     * @var CsrfTokenManagerInterface $csrfTokenManager
     */
    protected $csrfTokenManager;

    /**
     * @var string $tokenId
     */
    protected $tokenId;

    /**
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param string $tokenId
     */
    public function __construct(CsrfTokenManagerInterface $csrfTokenManager, $tokenId = 'link')
    {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->tokenId = $tokenId;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('link_attr', [$this, 'linkAttributes'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('link_csrf', [$this, 'linkCsrf'], ['is_safe' => ['html']]),
        );
    }

    /**
     * @param $method
     * @param string $confirm
     * @return string
     */
    public function linkAttributes($method, $confirm = 'Are you sure?')
    {
        $attr = sprintf('data-method="%s"', $method);
        if (false !== $confirm) {
            $attr .= sprintf(' data-confirm="%s"', $confirm);
        } else {
            $attr .= ' data-no-confirm';
        }
        
        return sprintf('%s data-csrf-token="%s"', $attr, $this->linkCsrf());
    }

    /**
     * @return CsrfToken
     */
    public function linkCsrf()
    {
        return $this->csrfTokenManager->getToken($this->tokenId)->getValue();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ite_js.twig.extension.link';
    }

}