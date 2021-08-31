<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Form\DataTransformer;

use EzSystems\EzPlatformAdminUi\Form\DataTransformer\PolicyTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;

class PolicyTransformerTest extends TestCase
{
    /**
     * @dataProvider transformDataProvider
     *
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
     * @dataProvider transformWithInvalidInputDataProvider
     *
     * @param $value
     */
    public function testTransformWithInvalidInput($value)
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a valid array of data.');

        $transformer = new PolicyTransformer();

        $transformer->transform($value);
    }

    /**
     * @dataProvider reverseTransformDataProvider
     *
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
     * @dataProvider reverseTransformWithInvalidInputDataProvider
     *
     * @param $value
     * @param $expectedMessage
     */
    public function testReverseTransformWithInvalidInput($value, $expectedMessage)
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage($expectedMessage);

        $transformer = new PolicyTransformer();

        $transformer->reverseTransform($value);
    }

    /**
     * @return array
     */
    public function transformDataProvider(): array
    {
        return [
            'policy' => [['id' => 123456, 'module' => 'module_name', 'function' => 'some_function'], '123456:module_name:some_function'],
            'null' => [null, null],
        ];
    }

    /**
     * @return array
     */
    public function reverseTransformDataProvider(): array
    {
        return [
            'string' => ['123456:module:function', ['id' => 123456, 'module' => 'module', 'function' => 'function']],
            'null' => [null, null],
        ];
    }

    /**
     * @return array
     */
    public function transformWithInvalidInputDataProvider(): array
    {
        return [
            'integer' => [123456],
            'bool' => [true],
            'float' => [12.34],
            'empty_array' => [[]],
            'object' => [new \stdClass()],
            'string' => ['some string'],
            'empty_string' => [''],
            'missing_id' => [['module' => 'module_name', 'function' => 'some_function']],
            'missing_module' => [['id' => 123456, 'function' => 'some_function']],
            'missing_function' => [['id' => 123456, 'module' => 'module_name']],
        ];
    }

    /**
     * @return array
     */
    public function reverseTransformWithInvalidInputDataProvider(): array
    {
        $stringExpected = 'Expected a string.';
        $atLeast3Parts = 'Policy string must contain at least 3 parts.';

        return [
            'integer' => [123456, $stringExpected],
            'bool' => [true, $stringExpected],
            'float' => [12.34, $stringExpected],
            'array' => [[], $stringExpected],
            'object' => [new \stdClass(), $stringExpected],
            '2_parts' => ['123456:module', $atLeast3Parts],
            '1_part' => ['123456', $atLeast3Parts],
            'empty_string' => ['', $atLeast3Parts],
        ];
    }
}
