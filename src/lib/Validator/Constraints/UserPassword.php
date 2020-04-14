<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Validator\Constraints;

use EzSystems\EzPlatformUser\Validator\Constraints\UserPassword as BaseUserPassword;

/**
 * @Annotation
 *
 * @deprecated Use EzSystems\EzPlatformUser\Validator\Constraints\UserPassword instead.
 */
class UserPassword extends BaseUserPassword
{
}
