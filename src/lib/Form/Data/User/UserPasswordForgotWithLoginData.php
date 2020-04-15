<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\User;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @deprecated Since eZ Platform 3.0 class moved to EzPlatformUser Bundle. Use it instead.
 *
 * @see \EzSystems\EzPlatformUser\Form\Data\UserPasswordForgotWithLoginData.
 */
class UserPasswordForgotWithLoginData
{
    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    private $login;

    /**
     * @param string|null $login
     */
    public function __construct(?string $login = null)
    {
        $this->login = $login;
    }

    /**
     * @param string|null $login
     */
    public function setLogin(?string $login): void
    {
        $this->login = $login;
    }

    /**
     * @return string|null
     */
    public function getLogin(): ?string
    {
        return $this->login;
    }
}
