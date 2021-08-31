<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Tests\ParamConverter;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\Values\User\RoleAssignment;
use EzSystems\EzPlatformAdminUiBundle\ParamConverter\RoleAssignmentParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RoleAssignmentParamConverterTest extends AbstractParamConverterTest
{
    const SUPPORTED_CLASS = RoleAssignment::class;
    const PARAMETER_NAME = 'roleAssignment';

    /** @var \EzSystems\EzPlatformAdminUiBundle\ParamConverter\RoleAssignmentParamConverter */
    protected $converter;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $serviceMock;

    protected function setUp(): void
    {
        $this->serviceMock = $this->createMock(RoleService::class);

        $this->converter = new RoleAssignmentParamConverter($this->serviceMock);
    }

    /**
     * @dataProvider dataProvider
     *
     * @param mixed $roleAssignmentId The role assignment identifier fetched from the request
     * @param int $roleAssignmentIdToLoad The role assignment identifier used to load the role assignment
     */
    public function testApply($roleAssignmentId, int $roleAssignmentIdToLoad)
    {
        $valueObject = $this->createMock(RoleAssignment::class);

        $this->serviceMock
            ->expects($this->once())
            ->method('loadRoleAssignment')
            ->with($roleAssignmentIdToLoad)
            ->willReturn($valueObject);

        $requestAttributes = [
            RoleAssignmentParamConverter::PRAMETER_ROLE_ASSIGNMENT_ID => $roleAssignmentId,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->assertTrue($this->converter->apply($request, $config));
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
        $this->expectExceptionMessage(sprintf('Role assignment %s not found.', $roleAssignmentId));

        $this->serviceMock
            ->expects($this->once())
            ->method('loadRoleAssignment')
            ->with($roleAssignmentId)
            ->willThrowException($this->createMock(NotFoundException::class));

        $requestAttributes = [
            RoleAssignmentParamConverter::PRAMETER_ROLE_ASSIGNMENT_ID => $roleAssignmentId,
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
