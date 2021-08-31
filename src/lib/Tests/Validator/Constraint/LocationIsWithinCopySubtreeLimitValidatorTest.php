<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Validator\Constraint;

use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\LocationIsWithinCopySubtreeLimit;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\LocationIsWithinCopySubtreeLimitValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class LocationIsWithinCopySubtreeLimitValidatorTest extends TestCase
{
    /** @var int */
    private $copyLimit;

    /** @var \eZ\Publish\API\Repository\SearchService|\PHPUnit\Framework\MockObject\MockObject */
    private $searchService;

    /** @var \Symfony\Component\Validator\Context\ExecutionContextInterface */
    private $executionContext;

    /** @var \EzSystems\EzPlatformAdminUi\Validator\Constraints\LocationIsContainerValidator */
    private $validator;

    /** @var \eZ\Publish\API\Repository\Values\Content\Location|\PHPUnit\Framework\MockObject\MockObject */
    private $location;

    protected function setUp(): void
    {
        $this->copyLimit = 10;
        $configResolver = $this->createMock(ConfigResolverInterface::class);
        $configResolver->method('getParameter')->with('subtree_operations.copy_subtree.limit')->willReturn($this->copyLimit);
        $this->searchService = $this->createMock(SearchService::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new LocationIsWithinCopySubtreeLimitValidator($this->searchService, $configResolver);
        $this->validator->initialize($this->executionContext);
        $this->location = $this
            ->getMockBuilder(Location::class)
            ->setMethodsExcept(['__get'])
            ->setConstructorArgs([['pathString' => '/1/2/3/']])
            ->getMock();
    }

    public function testValid()
    {
        $searchResults = $this
            ->getMockBuilder(SearchResult::class)
            ->setMethodsExcept(['__get'])
            ->setConstructorArgs([['totalCount' => 5]])
            ->getMock();

        $this->searchService->method('findLocations')->willReturn($searchResults);

        $this->executionContext
            ->expects($this->never())
            ->method('addViolation');

        $this->validator->validate($this->location, new LocationIsWithinCopySubtreeLimit());
    }

    public function testInvalid()
    {
        $searchResults = $this
            ->getMockBuilder(SearchResult::class)
            ->setMethodsExcept(['__get'])
            ->setConstructorArgs([['totalCount' => 15]])
            ->getMock();

        $this->searchService->method('findLocations')->willReturn($searchResults);

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
}
