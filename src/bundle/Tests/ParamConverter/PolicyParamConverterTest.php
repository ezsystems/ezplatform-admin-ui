<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Tests\ParamConverter;

use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\Values\User\Policy;
use eZ\Publish\Core\Repository\Values\User\Policy as UserPolicy;
use eZ\Publish\API\Repository\Values\User\Role;
use EzSystems\EzPlatformAdminUiBundle\ParamConverter\PolicyParamConverter;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PolicyParamConverterTest extends AbstractParamConverterTest
{
    const SUPPORTED_CLASS = Policy::class;
    const PARAMETER_NAME = 'policy';

    /** @var PolicyParamConverter */
    protected $converter;

    /** @var MockObject */
    protected $serviceMock;

    public function setUp()
    {
        $this->serviceMock = $this->createMock(RoleService::class);

        $this->converter = new PolicyParamConverter($this->serviceMock);
    }

    public function testApply()
    {
        $roleId = 42;
        $policyId = 53;
        $valueObject = $this->createMock(Role::class);
        $valueObject->expects(self::once())
            ->method('getPolicies')
            ->willReturn([new UserPolicy(['id' => $policyId])]);

        $this->serviceMock
            ->expects($this->once())
            ->method('loadRole')
            ->with($roleId)
            ->willReturn($valueObject);

        $requestAttributes = [
            PolicyParamConverter::PARAMETER_ROLE_ID => $roleId,
            PolicyParamConverter::PARAMETER_POLICY_ID => $policyId,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->converter->apply($request, $config);

        $this->assertInstanceOf(self::SUPPORTED_CLASS, $request->attributes->get(self::PARAMETER_NAME));
    }

    /**
     * @dataProvider attributeProvider
     *
     * @param $roleId
     * @param $policyId
     */
    public function testApplyWithWrongAttribute($roleId, $policyId)
    {
        $requestAttributes = [
            PolicyParamConverter::PARAMETER_ROLE_ID => $roleId,
            PolicyParamConverter::PARAMETER_POLICY_ID => $policyId,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->assertFalse($this->converter->apply($request, $config));
        $this->assertNull($request->attributes->get(self::PARAMETER_NAME));
    }

    public function testApplyWhenRoleNotFound()
    {
        $roleId = 42;
        $policyId = 53;

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage(sprintf('Role %s not found!', $roleId));

        $this->serviceMock
            ->expects($this->once())
            ->method('loadRole')
            ->with($roleId)
            ->willReturn(null);

        $requestAttributes = [
            PolicyParamConverter::PARAMETER_ROLE_ID => $roleId,
            PolicyParamConverter::PARAMETER_POLICY_ID => $policyId,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->converter->apply($request, $config);
    }

    public function testApplyWhenPolicyNotFound()
    {
        $roleId = 42;
        $policyId = 53;

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage(sprintf('Policy %s not found!', $policyId));

        $valueObject = $this->createMock(Role::class);
        $valueObject->expects(self::once())
            ->method('getPolicies')
            ->willReturn([new UserPolicy(['id' => 123])]);

        $this->serviceMock
            ->expects($this->once())
            ->method('loadRole')
            ->with($roleId)
            ->willReturn($valueObject);

        $requestAttributes = [
            PolicyParamConverter::PARAMETER_ROLE_ID => $roleId,
            PolicyParamConverter::PARAMETER_POLICY_ID => $policyId,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->converter->apply($request, $config);
    }

    /**
     * @return array
     */
    public function attributeProvider(): array
    {
        return [
            'empty_role_id' => [null, 53],
            'empty_policy_id' => [42, null],
        ];
    }
}
