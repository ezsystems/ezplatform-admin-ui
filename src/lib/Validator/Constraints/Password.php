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
 * @deprecated Since eZ Platform 3.0.2 class moved to EzPlatformUser Bundle. Use it instead.
 * @see \EzSystems\EzPlatformUser\Validator\Constraints\Password.
 */
class Password extends BasePassword
{
}
