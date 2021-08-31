<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Form\Data;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use PHPUnit\Framework\TestCase;

class FieldDefinitionDataTest extends TestCase
{
    public function testFieldDefinition()
    {
        $fieldDefinition = $this->getMockForAbstractClass(FieldDefinition::class);
        $data = new FieldDefinitionData(['fieldDefinition' => $fieldDefinition]);
        self::assertSame($fieldDefinition, $data->fieldDefinition);
    }

    public function testGetFieldTypeIdentifier()
    {
        $fieldTypeIdentifier = 'ezstring';
        $fieldDefinition = $this->getMockBuilder(FieldDefinition::class)
            ->setConstructorArgs([['fieldTypeIdentifier' => $fieldTypeIdentifier]])
            ->getMockForAbstractClass();
        $data = new FieldDefinitionData(['fieldDefinition' => $fieldDefinition]);
        self::assertSame($fieldTypeIdentifier, $data->getFieldTypeIdentifier());
    }
}
