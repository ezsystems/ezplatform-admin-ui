<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\EzPlatformAdminUi\Tests\Validator\Constraint;

use EzSystems\EzPlatformAdminUi\Validator\Constraints\UniqueLanguageCode;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;

class UniqueLanguageCodeTest extends TestCase
{
    public function testConstruct()
    {
        $constraint = new UniqueLanguageCode();
        self::assertSame('ez.language.code.unique', $constraint->message);
    }

    public function testValidateBy()
    {
        $constraint = new UniqueLanguageCode();
        self::assertSame('ezrepoforms.validator.unique_language_code', $constraint->validatedBy());
    }

    public function testGetTargets()
    {
        $constraint = new UniqueLanguageCode();
        self::assertSame(Constraint::CLASS_CONSTRAINT, $constraint->getTargets());
    }
}
