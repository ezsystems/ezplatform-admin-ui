<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Form\DataTransformer;

use eZ\Publish\API\Repository\FieldType;
use eZ\Publish\SPI\FieldType\Value;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\FieldType\FieldValueTransformer;
use PHPUnit\Framework\TestCase;
use stdClass;

class FieldValueTransformerTest extends TestCase
{
    public function testTransformNull()
    {
        $value = new stdClass();

        $fieldType = $this->createMock(FieldType::class);
        $fieldType
            ->expects($this->never())
            ->method('toHash');

        $result = (new FieldValueTransformer($fieldType))->transform($value);

        $this->assertNull($result);
    }

    public function testTransform()
    {
        $value = $this->createMock(Value::class);
        $valueHash = ['lorem' => 'Lorem ipsum dolor...'];

        $fieldType = $this->createMock(FieldType::class);
        $fieldType
            ->expects($this->once())
            ->method('toHash')
            ->with($value)
            ->willReturn($valueHash);

        $result = (new FieldValueTransformer($fieldType))->transform($value);

        $this->assertEquals($result, $valueHash);
    }

    public function testReverseTransformNull()
    {
        $emptyValue = $this->createMock(Value::class);

        $fieldType = $this->createMock(FieldType::class);
        $fieldType
            ->expects($this->once())
            ->method('getEmptyValue')
            ->willReturn($emptyValue);
        $fieldType
            ->expects($this->never())
            ->method('fromHash');

        $result = (new FieldValueTransformer($fieldType))->reverseTransform(null);

        $this->assertSame($emptyValue, $result);
    }

    public function testReverseTransform()
    {
        $value = 'Lorem ipsum dolor...';
        $expected = $this->createMock(Value::class);

        $fieldType = $this->createMock(FieldType::class);
        $fieldType
            ->expects($this->never())
            ->method('getEmptyValue');
        $fieldType
            ->expects($this->once())
            ->method('fromHash')
            ->willReturn($expected);

        $result = (new FieldValueTransformer($fieldType))->reverseTransform($value);

        $this->assertSame($expected, $result);
    }
}
