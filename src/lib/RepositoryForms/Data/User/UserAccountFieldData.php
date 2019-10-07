<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Data\User;

/**
 * User account field data value object.
 *
 * Used to store submitted user account values, since the clear password is not meant to be part of the
 * User\Value object.
 */
class UserAccountFieldData
{
    /** @var string */
    public $username;

    /** @var string */
    public $password;

    /** @var string */
    public $email;

    /** @var bool */
    public $enabled;

    /**
     * @param string $username
     * @param string $password
     * @param string $email
     * @param bool $enabled
     */
    public function __construct($username, $password, $email, $enabled = true)
    {
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->enabled = $enabled;
    }
}
