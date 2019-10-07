<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Validator\Constraint;

use EzSystems\EzPlatformAdminUi\Validator\Constraints\UserAccountPassword;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\UserAccountPasswordValidator;
use PHPUnit\Framework\TestCase;

class UserAccountPasswordTest extends TestCase
{
    /** @var \EzSystems\EzPlatformAdminUi\Validator\Constraints\Password */
    private $constraint;

    protected function setUp(): void
    {
        $this->constraint = new UserAccountPassword();
    }

    public function testConstruct()
    {
        $this->assertSame('ez.user.password.invalid', $this->constraint->message);
    }

    public function testValidatedBy()
    {
        $this->assertSame(UserAccountPasswordValidator::class, $this->constraint->validatedBy());
    }

    public function testGetTargets()
    {
        $this->assertSame([UserAccountPassword::CLASS_CONSTRAINT, UserAccountPassword::PROPERTY_CONSTRAINT], $this->constraint->getTargets());
    }
}
