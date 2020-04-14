<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Validator\Constraints;

use EzSystems\EzPlatformUser\Validator\Constraints\Password as BasePassword;

/**
 * @Annotation
 *
 * @deprecated
 * Use EzSystems\EzPlatformUser\Validator\Constraints\Password instead.
 */
class Password extends BasePassword
{
}
