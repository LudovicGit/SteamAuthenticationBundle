<?php

namespace Soljian\SteamAuthenticationBundle\Security\Firewall;

use Soljian\SteamAuthenticationBundle\Security\Authentication\Token\SteamUserToken;
use Soljian\SteamAuthenticationBundle\Security\Authentication\Validator\RequestValidatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @author Soljian
 */
class SteamListener
{
    /**
     * @var AuthenticationManagerInterface
     */
    private $authenticationManager;

    /**
     * @var string
     */
    private $loginRedirect;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /** @var RequestValidatorInterface */
    private $requestValidator;

    /**
     * @param AuthenticationManagerInterface $authenticationManager
     * @param RouterInterface $router
     * @param string $loginRedirect
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        AuthenticationManagerInterface $authenticationManager,
        RouterInterface $router,
        string $loginRedirect,
        TokenStorageInterface $tokenStorage,
        RequestValidatorInterface $requestValidator
    )
    {
        $this->authenticationManager = $authenticationManager;
        $this->router = $router;
        $this->loginRedirect = $loginRedirect;
        $this->tokenStorage = $tokenStorage;
        $this->requestValidator = $requestValidator;
    }

    public function __invoke(RequestEvent $event)
    {
        $request = $event->getRequest();

        $this->requestValidator->setRequest($request);

        if (!$this->requestValidator->validate()) {
            return;
        }

        $claimedId = str_replace('https://steamcommunity.com/openid/id/', '', $request->query->get('openid_claimed_id'));

        $token = new SteamUserToken();
        $token->setUsername($claimedId);

        $authToken = $this->authenticationManager->authenticate($token);
        $this->tokenStorage->setToken($authToken);


        $event->setResponse(new RedirectResponse(
            $this->router->generate($this->loginRedirect)
        ));
    }
}
