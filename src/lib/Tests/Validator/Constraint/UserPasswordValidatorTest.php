<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Validator\Constraint;

use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\User\User as APIUser;
use eZ\Publish\Core\MVC\Symfony\Security\ReferenceUserInterface;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\UserPassword;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\UserPasswordValidator;
use EzSystems\EzPlatformUser\Validator\Constraints\UserPasswordValidator as BaseUserPasswordValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class UserPasswordValidatorTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\eZ\Publish\API\Repository\UserService
     */
    private $userService;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var \Symfony\Component\Validator\Context\ExecutionContextInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $executionContext;

    /**
     * @var \EzSystems\EzPlatformAdminUi\Validator\Constraints\UserPasswordValidator
     */
    private $validator;

    protected function setUp(): void
    {
        $this->userService = $this->createMock(UserService::class);
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new UserPasswordValidator(
            new BaseUserPasswordValidator($this->userService, $this->tokenStorage)
        );
        $this->validator->initialize($this->executionContext);
    }

    /**
     * @dataProvider emptyDataProvider
     *
     * @param string|null $value
     */
    public function testEmptyValueType($value)
    {
        $this->userService
            ->expects($this->never())
            ->method('checkUserCredentials');
        $this->tokenStorage
            ->expects($this->never())
            ->method('getToken');
        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate($value, new UserPassword());
    }

    public function emptyDataProvider(): array
    {
        return [
            'empty_string' => [''],
            'null' => [null],
        ];
    }

    public function testValid()
    {
        $apiUser = $this->getMockForAbstractClass(APIUser::class, [], '', true, true, true, ['__get']);
        $apiUser->method('__get')->with($this->equalTo('login'))->willReturn('login');

        $user = $this->createMock(ReferenceUserInterface::class);
        $user->method('getAPIUser')->willReturn($apiUser);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $this->tokenStorage->method('getToken')->willReturn($token);
        $this->userService
            ->method('checkUserCredentials')
            ->with($apiUser, 'password')
            ->willReturn(true);

        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate('password', new UserPassword());
    }

    public function testInvalid()
    {
        $apiUser = $this->getMockForAbstractClass(APIUser::class, [], '', true, true, true, ['__get']);
        $apiUser->method('__get')->with($this->equalTo('login'))->willReturn('login');

        $user = $this->createMock(ReferenceUserInterface::class);
        $user->method('getAPIUser')->willReturn($apiUser);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $this->tokenStorage->method('getToken')->willReturn($token);

        $this->userService
            ->method('checkUserCredentials')
            ->with($apiUser, 'password')
            ->willReturn(false);

        $constraint = new UserPassword();
        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $this->executionContext
            ->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($constraintViolationBuilder);

        $this->validator->validate('password', new UserPassword());
    }
}
