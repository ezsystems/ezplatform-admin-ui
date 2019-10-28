<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Validator\Constraint;

use eZ\Publish\API\Repository\FieldType;
use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\Core\FieldType\ValidationError;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\FieldSettings;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\FieldSettingsValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class FieldSettingsValidatorTest extends TestCase
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
     * @var FieldSettingsValidator
     */
    private $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->fieldTypeService = $this->createMock(FieldTypeService::class);
        $this->validator = new FieldSettingsValidator($this->fieldTypeService);
        $this->validator->initialize($this->executionContext);
    }

    public function testNotFieldDefinitionData()
    {
        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate('foo', new FieldSettings());
    }

    public function testValid()
    {
        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $fieldTypeIdentifier = 'ezstring';
        $fieldDefinition = new FieldDefinition(['fieldTypeIdentifier' => $fieldTypeIdentifier]);
        $fieldSettings = ['foo' => 'bar'];
        $fieldDefData = new FieldDefinitionData(['identifier' => 'foo', 'fieldDefinition' => $fieldDefinition, 'fieldSettings' => $fieldSettings]);
        $fieldType = $this->createMock(FieldType::class);
        $this->fieldTypeService
            ->expects($this->once())
            ->method('getFieldType')
            ->with($fieldTypeIdentifier)
            ->willReturn($fieldType);
        $fieldType
            ->expects($this->once())
            ->method('validateFieldSettings')
            ->with($fieldSettings)
            ->willReturn([]);

        $this->validator->validate($fieldDefData, new FieldSettings());
    }

    public function testInvalid()
    {
        $fieldTypeIdentifier = 'ezstring';
        $fieldDefinition = new FieldDefinition(['fieldTypeIdentifier' => $fieldTypeIdentifier]);
        $fieldSettings = ['%foo%' => 'bar'];
        $fieldDefData = new FieldDefinitionData(['identifier' => 'foo', 'fieldDefinition' => $fieldDefinition, 'fieldSettings' => $fieldSettings]);
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
            ->method('validateFieldSettings')
            ->with($fieldSettings)
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

        $this->validator->validate($fieldDefData, new FieldSettings());
    }
}
