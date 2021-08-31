<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Validator\Constraint;

use EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeData;
use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\UniqueFieldDefinitionIdentifier;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\UniqueFieldDefinitionIdentifierValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class UniqueFieldDefinitionIdentifierValidatorTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $executionContext;

    /**
     * @var \EzSystems\EzPlatformAdminUi\Validator\Constraints\UniqueFieldDefinitionIdentifierValidator
     */
    private $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new UniqueFieldDefinitionIdentifierValidator();
        $this->validator->initialize($this->executionContext);
    }

    public function testNotFieldDefinitionData()
    {
        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate('foo', new UniqueFieldDefinitionIdentifier());
    }

    public function testValid()
    {
        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $contentTypeData = new ContentTypeData();
        $fieldDefData1 = new FieldDefinitionData(['identifier' => 'foo', 'contentTypeData' => $contentTypeData]);
        $contentTypeData->addFieldDefinitionData($fieldDefData1);
        $fieldDefData2 = new FieldDefinitionData(['identifier' => 'bar', 'contentTypeData' => $contentTypeData]);
        $contentTypeData->addFieldDefinitionData($fieldDefData2);
        $fieldDefData3 = new FieldDefinitionData(['identifier' => 'baz', 'contentTypeData' => $contentTypeData]);
        $contentTypeData->addFieldDefinitionData($fieldDefData3);

        $this->validator->validate($fieldDefData1, new UniqueFieldDefinitionIdentifier());
    }

    public function testInvalid()
    {
        $identifier = 'foo';
        $constraint = new UniqueFieldDefinitionIdentifier();
        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
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

        $contentTypeData = new ContentTypeData();
        $fieldDefData1 = new FieldDefinitionData(['identifier' => $identifier, 'contentTypeData' => $contentTypeData]);
        $contentTypeData->addFieldDefinitionData($fieldDefData1);
        $fieldDefData2 = new FieldDefinitionData(['identifier' => 'bar', 'contentTypeData' => $contentTypeData]);
        $contentTypeData->addFieldDefinitionData($fieldDefData2);
        $fieldDefData3 = new FieldDefinitionData(['identifier' => $identifier, 'contentTypeData' => $contentTypeData]);
        $contentTypeData->addFieldDefinitionData($fieldDefData3);

        $this->validator->validate($fieldDefData1, $constraint);
    }
}
