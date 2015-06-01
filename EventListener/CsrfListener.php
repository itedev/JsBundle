<?php

namespace ITE\JsBundle\EventListener;

use ITE\JsBundle\Annotation\CsrfSecure;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Class CsrfListener
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class CsrfListener
{
    /**
     * @var CsrfTokenManagerInterface $csrfTokenManager
     */
    protected $csrfTokenManager;

    /**
     * @var string $intention
     */
    protected $intention;

    /**
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param string $intention
     */
    public function __construct(CsrfTokenManagerInterface $csrfTokenManager, $intention = 'link')
    {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->intention = $intention;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        /** @var CsrfSecure $csrfSecure */
        if (null === $csrfSecure = $request->attributes->get('_csrf_secure')) {
            return;
        }

        $tokenId = $csrfSecure->getTokenId();
        $tokenValue = $request->request->get('_token');
        $token = new CsrfToken($tokenId, $tokenValue);

        if (false === $this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException('The CSRF token is invalid');
        }
    }

}