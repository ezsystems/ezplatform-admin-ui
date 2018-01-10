<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Tests\ParamConverter;

use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\Values\User\Role;
use EzSystems\EzPlatformAdminUiBundle\ParamConverter\RoleParamConverter;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RoleParamConverterTest extends AbstractParamConverterTest
{
    const SUPPORTED_CLASS = Role::class;
    const PARAMETER_NAME = 'role';

    /** @var RoleParamConverter */
    protected $converter;

    /** @var MockObject */
    protected $serviceMock;

    public function setUp()
    {
        $this->serviceMock = $this->createMock(RoleService::class);

        $this->converter = new RoleParamConverter($this->serviceMock);
    }

    public function testApply()
    {
        $roleId = 42;
        $valueObject = $this->createMock(Role::class);

        $this->serviceMock
            ->expects($this->once())
            ->method('loadRole')
            ->with($roleId)
            ->willReturn($valueObject);

        $requestAttributes = [
            RoleParamConverter::PARAMETER_ROLE_ID => $roleId,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->converter->apply($request, $config);

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
        $this->expectExceptionMessage(sprintf('Role %s not found!', $roleId));

        $this->serviceMock
            ->expects($this->once())
            ->method('loadRole')
            ->with($roleId)
            ->willReturn(null);

        $requestAttributes = [
            RoleParamConverter::PARAMETER_ROLE_ID => $roleId,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->converter->apply($request, $config);
    }
}
