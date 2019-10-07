<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\EzPlatformAdminUi\Tests\Validator\Constraint;

use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\Values\User\Role;
use eZ\Publish\API\Repository\Values\User\RoleDraft;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Role\RoleData;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\UniqueRoleIdentifier;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\UniqueRoleIdentifierValidator;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class UniqueRoleIdentifierValidatorTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $roleService;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $executionContext;

    /**
     * @var UniqueRoleIdentifierValidator
     */
    private $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->roleService = $this->createMock(RoleService::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new UniqueRoleIdentifierValidator($this->roleService);
        $this->validator->initialize($this->executionContext);
    }

    public function testNotRoleData()
    {
        $value = new stdClass();
        $this->roleService
            ->expects($this->never())
            ->method('loadRoleByIdentifier');
        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate($value, new UniqueRoleIdentifier());
    }

    public function testNullRoleIdentifier()
    {
        $value = new RoleData(['identifier' => null]);
        $this->roleService
            ->expects($this->never())
            ->method('loadRoleByIdentifier');
        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate($value, new UniqueRoleIdentifier());
    }

    public function testValid()
    {
        $identifier = 'foo_identifier';
        $value = new RoleData(['identifier' => $identifier]);
        $this->roleService
            ->expects($this->once())
            ->method('loadRoleByIdentifier')
            ->with($identifier)
            ->willThrowException(new NotFoundException('foo', 'bar'));
        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate($value, new UniqueRoleIdentifier());
    }

    public function testEditingRoleDraftFromExistingRoleIsValid()
    {
        $identifier = 'foo_identifier';
        $roleId = 123;
        $roleDraft = $this->getMockBuilder(RoleDraft::class)
            ->setConstructorArgs([['id' => $roleId]])
            ->getMockForAbstractClass();
        $value = new RoleData([
            'identifier' => $identifier,
            'roleDraft' => $roleDraft,
        ]);
        $returnedRole = $this->getMockBuilder(Role::class)
            ->setConstructorArgs([['id' => $roleId]])
            ->getMockForAbstractClass();
        $this->roleService
            ->expects($this->once())
            ->method('loadRoleByIdentifier')
            ->with($identifier)
            ->willReturn($returnedRole);
        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate($value, new UniqueRoleIdentifier());
    }

    public function testInvalid()
    {
        $identifier = 'foo_identifier';
        $roleDraft = $this->getMockBuilder(RoleDraft::class)
            ->setConstructorArgs([['id' => 456]])
            ->getMockForAbstractClass();
        $value = new RoleData([
            'identifier' => $identifier,
            'roleDraft' => $roleDraft,
        ]);
        $constraint = new UniqueRoleIdentifier();
        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $returnedRole = $this->getMockBuilder(Role::class)
            ->setConstructorArgs([['id' => 123]])
            ->getMockForAbstractClass();
        $this->roleService
            ->expects($this->once())
            ->method('loadRoleByIdentifier')
            ->with($identifier)
            ->willReturn($returnedRole);
        $this->executionContext
            ->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder
            ->expects($this->once())
            ->method('atPath')
            ->with('identifier')
            ->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with('%identifier%', $identifier)
            ->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder
            ->expects($this->once())
            ->method('addViolation');

        $this->validator->validate($value, $constraint);
    }
}
