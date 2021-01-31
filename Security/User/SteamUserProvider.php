<?php

namespace Soljian\SteamAuthenticationBundle\Security\User;

use Soljian\SteamAuthenticationBundle\User\SteamUserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Soljian\SteamAuthenticationBundle\Factory\UserFactory;
use Soljian\SteamAuthenticationBundle\Http\SteamApiClient;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @author Soljian
 */
class SteamUserProvider implements UserProviderInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var SteamApiClient
     */
    private $api;

    /**
     * @var string
     */
    private $userClass;

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @param EntityManagerInterface $entityManager
     * @param SteamApiClient         $steamApiClient
     * @param string                 $userClass
     * @param UserFactory            $userFactory
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        SteamApiClient $steamApiClient,
        string $userClass,
        UserFactory $userFactory
    )
    {
        $this->entityManager = $entityManager;
        $this->api = $steamApiClient;
        $this->userClass = $userClass;
        $this->userFactory = $userFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        $user = $this->entityManager->getRepository($this->userClass)->findOneBy(['steamId' => $username]);
        $userData = $this->api->loadProfile($username);
        if (null === $user) {
            $roleDefault = $this->entityManager->getRepository("App\Entity\Role")->findOneBy(['name' => "ROLE_USER"]);

            $user = $this->userFactory->getFromSteamApiResponse($userData, $roleDefault);

            $this->entityManager->persist($user);
        } else {
            $user->update($userData);
        }

        $this->entityManager->flush();

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof SteamUserInterface) {
            throw new UnsupportedUserException();
        }

        return $this->entityManager->getRepository($this->userClass)->findOneBy(['steamId' => $user->getSteamId()]);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $class === $this->userClass;
    }
}