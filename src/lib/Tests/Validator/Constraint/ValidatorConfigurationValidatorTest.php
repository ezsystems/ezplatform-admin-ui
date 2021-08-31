<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Validator\Constraint;

use eZ\Publish\API\Repository\FieldType;
use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\Core\FieldType\ValidationError;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\ValidatorConfiguration;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\ValidatorConfigurationValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class ValidatorConfigurationValidatorTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $executionContext;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $fieldTypeService;

    /**
     * @var \EzSystems\EzPlatformAdminUi\Validator\Constraints\ValidatorConfigurationValidator
     */
    private $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->fieldTypeService = $this->createMock(FieldTypeService::class);
        $this->validator = new ValidatorConfigurationValidator($this->fieldTypeService);
        $this->validator->initialize($this->executionContext);
    }

    public function testNotFieldDefinitionData()
    {
        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate('foo', new ValidatorConfiguration());
    }

    public function testValid()
    {
        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $fieldTypeIdentifier = 'ezstring';
        $fieldDefinition = new FieldDefinition(['fieldTypeIdentifier' => $fieldTypeIdentifier]);
        $validatorConfiguration = ['foo' => 'bar'];
        $fieldDefData = new FieldDefinitionData(['identifier' => 'foo', 'fieldDefinition' => $fieldDefinition, 'validatorConfiguration' => $validatorConfiguration]);
        $fieldType = $this->createMock(FieldType::class);
        $this->fieldTypeService
            ->expects($this->once())
            ->method('getFieldType')
            ->with($fieldTypeIdentifier)
            ->willReturn($fieldType);
        $fieldType
            ->expects($this->once())
            ->method('validateValidatorConfiguration')
            ->with($validatorConfiguration)
            ->willReturn([]);

        $this->validator->validate($fieldDefData, new ValidatorConfiguration());
    }

    public function testInvalid()
    {
        $fieldTypeIdentifier = 'ezstring';
        $fieldDefinition = new FieldDefinition(['fieldTypeIdentifier' => $fieldTypeIdentifier]);
        $validatorConfiguration = ['%foo%' => 'bar'];
        $fieldDefData = new FieldDefinitionData(['identifier' => 'foo', 'fieldDefinition' => $fieldDefinition, 'validatorConfiguration' => $validatorConfiguration]);
        $fieldType = $this->createMock(FieldType::class);
        $this->fieldTypeService
            ->expects($this->once())
            ->method('getFieldType')
            ->with($fieldTypeIdentifier)
            ->willReturn($fieldType);

        $errorParameter = 'bar';
        $errorMessage = 'error';
        $fieldType
            ->expects($this->once())
            ->method('validateValidatorConfiguration')
            ->with($validatorConfiguration)
            ->willReturn([new ValidationError($errorMessage, null, ['%foo%' => $errorParameter])]);

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

        $this->validator->validate($fieldDefData, new ValidatorConfiguration());
    }
}
