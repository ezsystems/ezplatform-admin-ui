<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Form\DataTransformer;

use eZ\Publish\SPI\Persistence\User\Policy;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\PolicyTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;

class PolicyTransformerTest extends TestCase
{
    /**
     * @dataProvider transformDataProvider
     * @param $value
     * @param $expected
     */
    public function testTransform($value, $expected)
    {
        $transformer = new PolicyTransformer();

        $result = $transformer->transform($value);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider reverseTransformDataProvider
     * @param $value
     * @param $expected
     */
    public function testReverseTransform($value, $expected)
    {
        $transformer = new PolicyTransformer();
        $result = $transformer->reverseTransform($value);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider wrongValueForReverseTransformDataProvider
     * @param $value
     * @param $expectedMessage
     */
    public function testReverseTransformWithWrongValue($value, $expectedMessage)
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage($expectedMessage);

        $transformer = new PolicyTransformer();

        $transformer->reverseTransform($value);
    }

    public function transformDataProvider()
    {
        return [
//            'policy' => [new Policy(['id' => 123456, 'module' => 'module_name', 'function' => 'some_function' ]), '123456'],
            'null' => [null, null],
        ];
    }

    public function reverseTransformDataProvider()
    {
        return [
            'string' => ['123456:module:function', ['id' => 123456, 'module' => 'module', 'function' => 'function']],
            'null' => [null, null],
        ];
    }

    public function wrongValueForReverseTransformDataProvider()
    {
        $stringExpected = 'Expected a string.';
        $atLeast3Parts = 'Policy string must contain at least 3 parts.';

        return [
            'integer' => [123456, $stringExpected],
            'bool' => [true, $stringExpected],
            'float' => [(float)12.34, $stringExpected],
            'array' => [[], $stringExpected],
            'object' => [new \stdClass(), $stringExpected],
            '2_parts' => ['123456:module', $atLeast3Parts],
            '1_part' => ['123456', $atLeast3Parts],
            'empty_string' => ['', $atLeast3Parts],
        ];
    }
}
