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
     * @param string|null $tokenId
     * @return string
     */
    public function linkAttributes($method, $confirm = 'Are you sure?', $tokenId = null)
    {
        $attr = sprintf('data-method="%s"', $method);
        if (false !== $confirm) {
            $attr .= sprintf(' data-confirm="%s"', $confirm);
        }
        
        return sprintf('%s data-csrf-token="%s"', $attr, $this->linkCsrf($tokenId));
    }

    /**
     * @param string|null $tokenId
     * @return CsrfToken
     */
    public function linkCsrf($tokenId = null)
    {
        $tokenId = $tokenId ? $tokenId : $this->tokenId;

        return $this->csrfTokenManager->getToken($tokenId)->getValue();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ite_js.twig.extension.link';
    }

}