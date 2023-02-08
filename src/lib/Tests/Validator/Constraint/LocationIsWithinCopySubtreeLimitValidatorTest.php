<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Validator\Constraint;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\LocationIsWithinCopySubtreeLimit;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\LocationIsWithinCopySubtreeLimitValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class LocationIsWithinCopySubtreeLimitValidatorTest extends TestCase
{
    private const COPY_LIMIT = 10;

    /** @var \eZ\Publish\API\Repository\LocationService|\PHPUnit\Framework\MockObject\MockObject */
    private $locationService;

    /** @var \Symfony\Component\Validator\Context\ExecutionContextInterface */
    private $executionContext;

    /** @var \EzSystems\EzPlatformAdminUi\Validator\Constraints\LocationIsContainerValidator */
    private $validator;

    /** @var \eZ\Publish\API\Repository\Values\Content\Location|\PHPUnit\Framework\MockObject\MockObject */
    private $location;

    protected function setUp(): void
    {
        $configResolver = $this->createMock(ConfigResolverInterface::class);
        $configResolver
            ->method('getParameter')
            ->with('subtree_operations.copy_subtree.limit')
            ->willReturn(self::COPY_LIMIT);
        $this->locationService = $this->createMock(LocationService::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new LocationIsWithinCopySubtreeLimitValidator(
            $this->locationService,
            $configResolver
        );
        $this->validator->initialize($this->executionContext);
        $this->location = $this
            ->getMockBuilder(Location::class)
            ->setMethodsExcept(['__get'])
            ->setConstructorArgs([['pathString' => '/1/2/3/']])
            ->getMock();
    }

    public function testValid(): void
    {
        $this->locationService->method('count')->willReturn(5);

        $this->executionContext
            ->expects($this->never())
            ->method('addViolation');

        $this->mockLocationContentContentTypeIsContainer($this->location);

        $this->validator->validate($this->location, new LocationIsWithinCopySubtreeLimit());
    }

    public function testInvalid(): void
    {
        $this->locationService->method('count')->willReturn(15);

        $constraintViolationBuilder = $this
            ->getMockBuilder(ConstraintViolationBuilderInterface::class)
            ->getMock();

        $constraintViolationBuilder
            ->method('setParameter')
            ->willReturn($constraintViolationBuilder);

        $this->executionContext
            ->method('buildViolation')
            ->willReturn($constraintViolationBuilder);

        $this->executionContext
            ->expects($this->once())
            ->method('buildViolation');

        $this->validator->validate($this->location, new LocationIsWithinCopySubtreeLimit());
    }

    private function mockLocationContentContentTypeIsContainer(MockObject $location): void
    {
        $contentType = $this->createMock(ContentType::class);
        $contentType->method('isContainer')->willReturn(true);
        $contentInfo = $this->createMock(ContentInfo::class);
        $contentInfo->method('getContentType')->willReturn($contentType);

        $location->method('getContentInfo')->willReturn($contentInfo);
    }
}
