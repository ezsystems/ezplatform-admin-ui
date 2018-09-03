<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UserSetting;

use eZ\Publish\API\Repository\Values\ValueObject;

class UserSetting extends ValueObject
{
    /** @var string */
    protected $identifier;

    /** @var string */
    protected $name;

    /** @var string */
    protected $description;

    /** @var string */
    protected $value;
}
