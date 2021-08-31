<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Validator\Constraint;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeDraft;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeData;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\UniqueContentTypeIdentifier;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\UniqueContentTypeIdentifierValidator;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class UniqueContentTypeIdentifierValidatorTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $contentTypeService;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $executionContext;

    /**
     * @var \EzSystems\EzPlatformAdminUi\Validator\Constraints\UniqueContentTypeIdentifierValidator
     */
    private $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->contentTypeService = $this->createMock(ContentTypeService::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new UniqueContentTypeIdentifierValidator($this->contentTypeService);
        $this->validator->initialize($this->executionContext);
    }

    public function testNotContentTypeData()
    {
        $value = new stdClass();
        $this->contentTypeService
            ->expects($this->never())
            ->method('loadContentTypeByIdentifier');
        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate($value, new UniqueContentTypeIdentifier());
    }

    public function testNullContentTypeIdentifier()
    {
        $value = new ContentTypeData(['identifier' => null]);
        $this->contentTypeService
            ->expects($this->never())
            ->method('loadContentTypeByIdentifier');
        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate($value, new UniqueContentTypeIdentifier());
    }

    public function testValid()
    {
        $identifier = 'foo_identifier';
        $value = new ContentTypeData(['identifier' => $identifier]);
        $this->contentTypeService
            ->expects($this->once())
            ->method('loadContentTypeByIdentifier')
            ->with($identifier)
            ->willThrowException(new NotFoundException('foo', 'bar'));
        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate($value, new UniqueContentTypeIdentifier());
    }

    public function testEditingContentTypeDraftFromExistingContentTypeIsValid()
    {
        $identifier = 'foo_identifier';
        $contentTypeId = 123;
        $contentTypeDraft = $this->getMockBuilder(ContentTypeDraft::class)
            ->setConstructorArgs([['id' => $contentTypeId]])
            ->getMockForAbstractClass();
        $value = new ContentTypeData(['identifier' => $identifier, 'contentTypeDraft' => $contentTypeDraft]);
        $returnedContentType = $this->getMockBuilder(ContentType::class)
            ->setConstructorArgs([['id' => $contentTypeId]])
            ->getMockForAbstractClass();
        $this->contentTypeService
            ->expects($this->once())
            ->method('loadContentTypeByIdentifier')
            ->with($identifier)
            ->willReturn($returnedContentType);
        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate($value, new UniqueContentTypeIdentifier());
    }

    public function testInvalid()
    {
        $identifier = 'foo_identifier';
        $contentTypeDraft = $this->getMockBuilder(ContentTypeDraft::class)
            ->setConstructorArgs([['id' => 456]])
            ->getMockForAbstractClass();
        $value = new ContentTypeData(['identifier' => $identifier, 'contentTypeDraft' => $contentTypeDraft]);
        $constraint = new UniqueContentTypeIdentifier();
        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $returnedContentType = $this->getMockBuilder(ContentType::class)
            ->setConstructorArgs([['id' => 123]])
            ->getMockForAbstractClass();
        $this->contentTypeService
            ->expects($this->once())
            ->method('loadContentTypeByIdentifier')
            ->with($identifier)
            ->willReturn($returnedContentType);
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
