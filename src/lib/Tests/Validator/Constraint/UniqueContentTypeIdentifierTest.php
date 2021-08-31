<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Validator\Constraint;

use EzSystems\EzPlatformAdminUi\Validator\Constraints\UniqueContentTypeIdentifier;
use PHPUnit\Framework\TestCase;

class UniqueContentTypeIdentifierTest extends TestCase
{
    public function testConstruct()
    {
        $constraint = new UniqueContentTypeIdentifier();
        self::assertSame('ez.content_type.identifier.unique', $constraint->message);
    }

    public function testValidatedBy()
    {
        $constraint = new UniqueContentTypeIdentifier();
        self::assertSame('ezplatform.content_forms.validator.unique_content_type_identifier', $constraint->validatedBy());
    }

    public function testGetTargets()
    {
        $constraint = new UniqueContentTypeIdentifier();
        self::assertSame(UniqueContentTypeIdentifier::CLASS_CONSTRAINT, $constraint->getTargets());
    }
}
