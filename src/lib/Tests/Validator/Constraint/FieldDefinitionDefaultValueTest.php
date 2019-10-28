<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Validator\Constraint;

use EzSystems\EzPlatformAdminUi\Validator\Constraints\FieldDefinitionDefaultValue;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;

class FieldDefinitionDefaultValueTest extends TestCase
{
    public function testConstruct()
    {
        $constraint = new FieldDefinitionDefaultValue();
        self::assertSame('ez.field_definition.default_field_value', $constraint->message);
    }

    public function testValidatedBy()
    {
        $constraint = new FieldDefinitionDefaultValue();
        self::assertSame('ezplatform.content_forms.validator.default_field_value', $constraint->validatedBy());
    }

    public function testGetTargets()
    {
        $constraint = new FieldDefinitionDefaultValue();
        self::assertSame(Constraint::CLASS_CONSTRAINT, $constraint->getTargets());
    }
}
