<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Validator\Constraint;

use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\User\PasswordValidationContext;
use eZ\Publish\Core\FieldType\ValidationError;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\User\UserAccountFieldData;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\UserAccountPassword;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\UserAccountPasswordValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class UserAccountPasswordValidatorTest extends TestCase
{
    /** @var \eZ\Publish\API\Repository\UserService|\PHPUnit\Framework\MockObject\MockObject */
    private $userService;

    /** @var \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\Validator\Context\ExecutionContextInterface */
    private $executionContext;

    /** @var \EzSystems\EzPlatformAdminUi\Validator\Constraints\UserAccountPasswordValidator */
    private $validator;

    /**
     * @dataProvider dataProviderForValidateNotSupportedValueType
     */
    public function testValidateShouldBeSkipped($value)
    {
        $this->userService
            ->expects($this->never())
            ->method('validatePassword');

        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate($value, new UserAccountPassword());
    }

    protected function setUp(): void
    {
        $this->userService = $this->createMock(UserService::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new UserAccountPasswordValidator($this->userService);
        $this->validator->initialize($this->executionContext);
    }

    public function dataProviderForValidateNotSupportedValueType(): array
    {
        return [
            [new \stdClass()],
            [null],
            [''],
        ];
    }

    public function testValid()
    {
        $userAccount = new UserAccountFieldData('user', 'pass', 'user@ez.no');
        $contentType = $this->createMock(ContentType::class);

        $this->userService
            ->expects($this->once())
            ->method('validatePassword')
            ->willReturnCallback(function ($actualPassword, $actualContext) use ($userAccount, $contentType) {
                $this->assertEquals($userAccount->password, $actualPassword);
                $this->assertInstanceOf(PasswordValidationContext::class, $actualContext);
                $this->assertSame($contentType, $actualContext->contentType);

                return [];
            });

        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate($userAccount, new UserAccountPassword([
            'contentType' => $contentType,
        ]));
    }

    public function testInvalid()
    {
        $contentType = $this->createMock(ContentType::class);
        $userAccount = new UserAccountFieldData('user', 'pass', 'user@ez.no');
        $errorParameter = 'foo';
        $errorMessage = 'error';

        $this->userService
            ->expects($this->once())
            ->method('validatePassword')
            ->willReturnCallback(function ($actualPassword, $actualContext) use ($userAccount, $contentType, $errorMessage, $errorParameter) {
                $this->assertEquals($userAccount->password, $actualPassword);
                $this->assertInstanceOf(PasswordValidationContext::class, $actualContext);
                $this->assertSame($contentType, $actualContext->contentType);

                return [
                    new ValidationError($errorMessage, null, ['%foo%' => $errorParameter]),
                ];
            });

        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $this->executionContext
            ->expects($this->once())
            ->method('buildViolation')
            ->willReturn($constraintViolationBuilder);
        $this->executionContext
            ->expects($this->once())
            ->method('buildViolation')
            ->with($errorMessage)
            ->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder
            ->expects($this->once())
            ->method('setParameters')
            ->with(['%foo%' => $errorParameter])
            ->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder
            ->expects($this->once())
            ->method('addViolation');

        $this->validator->validate($userAccount, new UserAccountPassword([
            'contentType' => $contentType,
        ]));
    }
}
