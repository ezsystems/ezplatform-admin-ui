<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Form\DataTransformer;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content as API;
use eZ\Publish\API\Repository\Values\User\User;
use eZ\Publish\Core\Repository\Values\Content as Core;
use eZ\Publish\Core\Repository\Values\User\User as CoreUser;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\UserTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;

class UserTransformerTest extends TestCase
{
    /** @var \EzSystems\EzPlatformAdminUi\Form\DataTransformer\UserTransformer */
    private $userTransformer;

    protected function setUp(): void
    {
        /** @var \eZ\Publish\API\Repository\UserService|\PHPUnit\Framework\MockObject\MockObject $userService */
        $userService = $this->createMock(UserService::class);
        $userService->expects(self::any())
            ->method('loadUser')
            ->with(123456)
            ->willReturn($this->generateUser(123456));

        $this->userTransformer = new UserTransformer($userService);
    }

    /**
     * @dataProvider transformDataProvider
     *
     * @param $value
     * @param $expected
     */
    public function testTransform($value, $expected)
    {
        $result = $this->userTransformer->transform($value);

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
        $this->expectExceptionMessage('Expected a ' . User::class . ' object.');

        $this->userTransformer->transform($value);
    }

    /**
     * @dataProvider reverseTransformDataProvider
     *
     * @param $value
     * @param $expected
     */
    public function testReverseTransform($value, $expected)
    {
        $result = $this->userTransformer->reverseTransform($value);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider reverseTransformWithInvalidInputDataProvider
     *
     * @param $value
     */
    public function testReverseTransformWithInvalidInput($value)
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a numeric string.');

        $this->userTransformer->reverseTransform($value);
    }

    public function testReverseTransformWithNotFoundException()
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('User not found');

        /** @var \eZ\Publish\API\Repository\UserService|\PHPUnit\Framework\MockObject\MockObject $service */
        $service = $this->createMock(UserService::class);
        $service->method('loadUser')
            ->will($this->throwException(new class('User not found') extends NotFoundException {
            }));

        $transformer = new UserTransformer($service);

        $transformer->reverseTransform(654321);
    }

    /**
     * @return array
     */
    public function transformDataProvider(): array
    {
        $user = $this->generateUser(123456);

        return [
            'user_with_id' => [$user, 123456],
            'null' => [null, null],
        ];
    }

    /**
     * @return array
     */
    public function reverseTransformDataProvider(): array
    {
        $user = $this->generateUser(123456);

        return [
            'integer' => [123456, $user],
            'null' => [null, null],
        ];
    }

    /**
     * @return array
     */
    public function transformWithInvalidInputDataProvider(): array
    {
        return [
            'string' => ['string'],
            'integer' => [123456],
            'bool' => [true],
            'float' => [12.34],
            'array' => [[]],
            'object' => [new \stdClass()],
        ];
    }

    /**
     * @return array
     */
    public function reverseTransformWithInvalidInputDataProvider(): array
    {
        return [
            'string' => ['string'],
            'bool' => [true],
            'array' => [['element']],
            'object' => [new \stdClass()],
            'user' => [$this->generateUser()],
        ];
    }

    /**
     * @param int $id
     *
     * @return \eZ\Publish\API\Repository\Values\User\User
     */
    private function generateUser(int $id = null): User
    {
        $contentInfo = new API\ContentInfo(['id' => $id]);
        $versionInfo = new Core\VersionInfo(['contentInfo' => $contentInfo]);
        $content = new Core\Content(['versionInfo' => $versionInfo]);

        return new CoreUser(['content' => $content]);
    }
}
