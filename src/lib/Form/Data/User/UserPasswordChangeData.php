<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\User;

use Symfony\Component\Validator\Constraints as Assert;
use EzSystems\EzPlatformAdminUi\Validator\Constraints as AdminUiAssert;

class UserPasswordChangeData
{
    /**
     * @AdminUiAssert\UserPassword()
     * @Assert\NotBlank()
     *
     * @var string
     */
    private $oldPassword;

    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    private $newPassword;

    /**
     * @param string|null $oldPassword
     * @param string|null $newPassword
     */
    public function __construct(?string $oldPassword = null, ?string $newPassword = null)
    {
        $this->oldPassword = $oldPassword;
        $this->newPassword = $newPassword;
    }

    /**
     * @param string|null $oldPassword
     */
    public function setOldPassword(?string $oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }

    /**
     * @param string|null $newPassword
     */
    public function setNewPassword(?string $newPassword): void
    {
        $this->newPassword = $newPassword;
    }

    /**
     * @return string|null
     */
    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    /**
     * @return string|null
     */
    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }
}
