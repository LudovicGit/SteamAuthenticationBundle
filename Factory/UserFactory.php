<?php

namespace Soljian\SteamAuthenticationBundle\Factory;

use Soljian\SteamAuthenticationBundle\Exception\InvalidUserClassException;
use Soljian\SteamAuthenticationBundle\User\SteamUserInterface;

/**
 * @author Soljian
 */
class UserFactory
{
    /**
     * @var string
     */
    private $userClass;

    /**
     * @param string $userClass
     */
    public function __construct(string $userClass)
    {
        $this->userClass = $userClass;
    }

    /**
     * @param array $userData
     *
     * @return SteamUserInterface
     *
     * @throws InvalidUserClassException
     */
    public function getFromSteamApiResponse(array $userData, $roleDefault)
    {
        $user = new $this->userClass;
        if (!$user instanceof SteamUserInterface) {
            throw new InvalidUserClassException($this->userClass);
        }

        $user->setSteamId($userData['steamid']);
        $user->setCommunityVisibilityState($userData['communityvisibilitystate']);
        $user->setProfileState(isset($userData['profilestate']) ? $userData['profilestate'] : 0);
        $user->setProfileName($userData['personaname']);
        $user->setLastLogOff(
            isset($userData['lastlogoff']) ? $userData['lastlogoff'] : 0
        );
        $user->setCommentPermission(
            isset($userData['commentpermission']) ? $userData['commentpermission'] : 0
        );
        $user->setProfileUrl($userData['profileurl']);
        $user->setAvatar($userData['avatarfull']);
        $user->setPersonaState(isset($userData['personastate']) ? $userData['personastate'] : 0);
        $user->setPrimaryClanId(
            isset($userData['primaryclanid']) ? $userData['primaryclanid'] : null
        );
        $user->setJoinDate(
            isset($userData['timecreated']) ? $userData['timecreated'] : null
        );
        $user->setCountryCode(
            isset($userData['loccountrycode']) ? $userData['loccountrycode'] : null
        );
        $user->addRole($roleDefault);

        return $user;
    }
}
