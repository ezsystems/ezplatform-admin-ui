<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Tests\ParamConverter;

use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\Values\User\RoleAssignment;
use EzSystems\EzPlatformAdminUiBundle\ParamConverter\RoleAssignmentParamConverter;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RoleAssignmentParamConverterTest extends AbstractParamConverterTest
{
    const SUPPORTED_CLASS = RoleAssignment::class;
    const PARAMETER_NAME = 'roleAssignment';

    /** @var RoleAssignmentParamConverter */
    protected $converter;

    /** @var MockObject */
    protected $serviceMock;

    public function setUp()
    {
        $this->serviceMock = $this->createMock(RoleService::class);

        $this->converter = new RoleAssignmentParamConverter($this->serviceMock);
    }

    public function testApply()
    {
        $roleAssignmentId = 42;
        $valueObject = $this->createMock(RoleAssignment::class);

        $this->serviceMock
            ->expects($this->once())
            ->method('loadRoleAssignment')
            ->with($roleAssignmentId)
            ->willReturn($valueObject);

        $requestAttributes = [
            RoleAssignmentParamConverter::PRAMETER_ROLE_ASSIGNMENT_ID => $roleAssignmentId,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->converter->apply($request, $config);

        $this->assertInstanceOf(self::SUPPORTED_CLASS, $request->attributes->get(self::PARAMETER_NAME));
    }

    public function testApplyWithWrongAttribute()
    {
        $requestAttributes = [
            RoleAssignmentParamConverter::PRAMETER_ROLE_ASSIGNMENT_ID => null,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->assertFalse($this->converter->apply($request, $config));
        $this->assertNull($request->attributes->get(self::PARAMETER_NAME));
    }

    public function testApplyWhenNotFound()
    {
        $roleAssignmentId = 42;

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage(sprintf('Role assignment %s not found!', $roleAssignmentId));

        $this->serviceMock
            ->expects($this->once())
            ->method('loadRoleAssignment')
            ->with($roleAssignmentId)
            ->willReturn(null);

        $requestAttributes = [
            RoleAssignmentParamConverter::PRAMETER_ROLE_ASSIGNMENT_ID => $roleAssignmentId,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->converter->apply($request, $config);
    }
}
