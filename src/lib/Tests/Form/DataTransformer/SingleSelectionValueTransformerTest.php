<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\EzPlatformAdminUi\Tests\Form\DataTransformer;

use eZ\Publish\Core\FieldType\Selection\Value;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\FieldType\SingleSelectionValueTransformer;
use PHPUnit\Framework\TestCase;

class SingleSelectionValueTransformerTest extends TestCase
{
    public function transformProvider()
    {
        return [
            [0],
            [1],
            [42],
        ];
    }

    /**
     * @dataProvider transformProvider
     */
    public function testTransform($value)
    {
        $transformer = new SingleSelectionValueTransformer();
        self::assertSame($value, $transformer->transform(new Value([$value])));
    }

    /**
     * @dataProvider transformProvider
     */
    public function testReverseTransform($value)
    {
        $transformer = new SingleSelectionValueTransformer();
        $expectedValue = new Value([$value]);
        self::assertEquals($expectedValue, $transformer->reverseTransform($value));
    }

    public function transformNullProvider()
    {
        return [
            [new Value()],
            [[]],
            [false],
            [''],
        ];
    }

    /**
     * @dataProvider transformNullProvider
     */
    public function testTransformNull($value)
    {
        $transformer = new SingleSelectionValueTransformer();
        self::assertNull($transformer->transform($value));
    }

    public function testReverseTransformNull()
    {
        $transformer = new SingleSelectionValueTransformer();
        self::assertNull($transformer->reverseTransform(null));
    }
}
