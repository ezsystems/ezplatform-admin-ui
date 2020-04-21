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
use EzSystems\EzPlatformAdminUi\Validator\Constraints\Password;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\PasswordValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;
use EzSystems\EzPlatformUser\Validator\Constraints\PasswordValidator as BasePasswordValidator;

class PasswordValidatorTest extends TestCase
{
    /** @var \eZ\Publish\API\Repository\UserService|\PHPUnit\Framework\MockObject\MockObject */
    private $userService;

    /** @var \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\Validator\Context\ExecutionContextInterface */
    private $executionContext;

    /** @var \EzSystems\EzPlatformAdminUi\Validator\Constraints\PasswordValidator */
    private $validator;

    protected function setUp(): void
    {
        $this->userService = $this->createMock(UserService::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new PasswordValidator(new BasePasswordValidator($this->userService));
        $this->validator->initialize($this->executionContext);
    }

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

        $this->validator->validate($value, new Password());
    }

    public function testValid()
    {
        $password = 'pass';
        $contentType = $this->createMock(ContentType::class);

        $this->userService
            ->expects($this->once())
            ->method('validatePassword')
            ->willReturnCallback(function ($actualPassword, $actualContext) use ($password, $contentType) {
                $this->assertEquals($password, $actualPassword);
                $this->assertInstanceOf(PasswordValidationContext::class, $actualContext);
                $this->assertSame($contentType, $actualContext->contentType);

                return [];
            });

        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate($password, new Password([
            'contentType' => $contentType,
        ]));
    }

    public function testInvalid()
    {
        $contentType = $this->createMock(ContentType::class);
        $password = 'pass';
        $errorParameter = 'foo';
        $errorMessage = 'error';

        $this->userService
            ->expects($this->once())
            ->method('validatePassword')
            ->willReturnCallback(function ($actualPassword, $actualContext) use ($password, $contentType, $errorMessage, $errorParameter) {
                $this->assertEquals($password, $actualPassword);
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

        $this->validator->validate('pass', new Password([
            'contentType' => $contentType,
        ]));
    }

    public function dataProviderForValidateNotSupportedValueType(): array
    {
        return [
            [new \stdClass()],
            [null],
            [''],
        ];
    }
}
