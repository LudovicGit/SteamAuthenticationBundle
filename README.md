
# SteamAuthenticationBundle
A Symfony Bundle that provides authentication via Steam for your application.

## Installation & Configuration

Just require the bundle via Composer and use the given flex recipe during the install process.

`composer require soljian/steam-authentication-bundle`

----------
In your `.env`  file a new entry for your Steam API key was generated. You can obtain your Steam API key here: https://steamcommunity.com/dev/apikey

**login_route** The route the user is redirected to after Steam Login

**login_redirect** The route the user is redirected to if the login was successfull

**user_class** Classname of your User Entity

**request_validator_class** Classname of RequestValidatorInterface class. If it isn't set then `Soljian\SteamAuthenticationBundle\Security\Authentication\Validator\RequestValidator` will be used.

----------
Make sure your User Entity extends from the `Soljian\SteamAuthenticationBundle\User\AbstractSteamUser` class
```php
<?php

namespace App\Entity;

use Soljian\SteamAuthenticationBundle\User\AbstractSteamUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\Role;

/**
 * @author Soljian
 *
 * @ORM\Entity()
 */
class User extends AbstractSteamUser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->roles = [];
    }
    
    /**
     * @return array
     */
    public function getRoles(): array
    {
        $roles = [];
        foreach ($this->roles as $role) {
            $roles[] = new Role($role);
        }

        return $roles;
    }
}
```


----------

Finally you just have to configure your firewall. A working example might looks like this
```yaml
security:
    providers:
        steam_user_provider:
            id: Soljian\SteamAuthenticationBundle\Security\User\SteamUserProvider
    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false
        main:
            anonymous: ~
            pattern: ^/
            provider: steam_user_provider
            steam: true
            logout:
                path:   /logout
                target: /

```

----------

To display the "Login via Steam" button just include this snippet in your template
```twig
{% include '@SoljianSteamAuthentication/login.html.twig' with { 'btn': 1 } %}
```
You can choose between two images (1 or 2) that will be display as button. Or simply enter your own text.
