<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\User;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @deprecated Since eZ Platform 3.0.2 class moved to EzPlatformUser Bundle. Use it instead.
 *
 * @see \EzSystems\EzPlatformUser\Form\Data\UserPasswordForgotData.
 */
class UserPasswordForgotData
{
    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    private $email;

    /**
     * @param string|null $email
     */
    public function __construct(?string $email = null)
    {
        $this->email = $email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }
}
