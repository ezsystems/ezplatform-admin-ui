<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Tests\ParamConverter;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\Values\User\Role;
use EzSystems\EzPlatformAdminUiBundle\ParamConverter\RoleParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RoleParamConverterTest extends AbstractParamConverterTest
{
    const SUPPORTED_CLASS = Role::class;
    const PARAMETER_NAME = 'role';

    /** @var \EzSystems\EzPlatformAdminUiBundle\ParamConverter\RoleParamConverter */
    protected $converter;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $serviceMock;

    protected function setUp(): void
    {
        $this->serviceMock = $this->createMock(RoleService::class);

        $this->converter = new RoleParamConverter($this->serviceMock);
    }

    /**
     * @dataProvider dataProvider
     *
     * @param mixed $roleId The role identifier fetched from the request
     * @param int $roleIdToLoad The role identifier used to load the role
     */
    public function testApply($roleId, int $roleIdToLoad)
    {
        $valueObject = $this->createMock(Role::class);

        $this->serviceMock
            ->expects($this->once())
            ->method('loadRole')
            ->with($roleIdToLoad)
            ->willReturn($valueObject);

        $requestAttributes = [
            RoleParamConverter::PARAMETER_ROLE_ID => $roleId,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->assertTrue($this->converter->apply($request, $config));
        $this->assertInstanceOf(self::SUPPORTED_CLASS, $request->attributes->get(self::PARAMETER_NAME));
    }

    public function testApplyWithWrongAttribute()
    {
        $requestAttributes = [
            RoleParamConverter::PARAMETER_ROLE_ID => null,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->assertFalse($this->converter->apply($request, $config));
        $this->assertNull($request->attributes->get(self::PARAMETER_NAME));
    }

    public function testApplyWhenNotFound()
    {
        $roleId = 42;

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage(sprintf('Role %s not found.', $roleId));

        $this->serviceMock
            ->expects($this->once())
            ->method('loadRole')
            ->with($roleId)
            ->willThrowException($this->createMock(NotFoundException::class));

        $requestAttributes = [
            RoleParamConverter::PARAMETER_ROLE_ID => $roleId,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->converter->apply($request, $config);
    }

    public function dataProvider(): array
    {
        return [
            'integer' => [42, 42],
            'number_as_string' => ['42', 42],
            'string' => ['42k', 42],
        ];
    }
}
