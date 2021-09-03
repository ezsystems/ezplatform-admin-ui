<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\AdminUi\Validator\Constraint;

use Ibexa\AdminUi\Validator\Constraints\ValidatorConfiguration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;

class ValidatorConfigurationTest extends TestCase
{
    public function testConstruct()
    {
        $constraint = new ValidatorConfiguration();
        self::assertSame('ez.field_definition.validator_configuration', $constraint->message);
    }

    public function testValidatedBy()
    {
        $constraint = new ValidatorConfiguration();
        self::assertSame('ezplatform.content_forms.validator.validator_configuration', $constraint->validatedBy());
    }

    public function testGetTargets()
    {
        $constraint = new ValidatorConfiguration();
        self::assertSame(Constraint::CLASS_CONSTRAINT, $constraint->getTargets());
    }
}

class_alias(ValidatorConfigurationTest::class, 'EzSystems\EzPlatformAdminUi\Tests\Validator\Constraint\ValidatorConfigurationTest');
