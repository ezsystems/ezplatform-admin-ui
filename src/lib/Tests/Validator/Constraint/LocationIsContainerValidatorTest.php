<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Validator\Constraint;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\LocationIsContainer;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\LocationIsContainerValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class LocationIsContainerValidatorTest extends TestCase
{
    /** @var \eZ\Publish\API\Repository\ContentTypeService|\PHPUnit\Framework\MockObject\MockObject */
    private $contentTypeService;

    /** @var \Symfony\Component\Validator\Context\ExecutionContextInterface */
    private $executionContext;

    /** @var \EzSystems\EzPlatformAdminUi\Validator\Constraints\LocationIsContainerValidator */
    private $validator;

    /** @var \eZ\Publish\API\Repository\Values\Content\Location|\PHPUnit\Framework\MockObject\MockObject */
    private $location;

    protected function setUp()
    {
        $this->contentTypeService = $this->createMock(ContentTypeService::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new LocationIsContainerValidator($this->contentTypeService);
        $this->validator->initialize($this->executionContext);
        $this->location = $this->createMock(Location::class);
        $this->location
            ->method('getContentInfo')
            ->willReturn(
                $this->createMock(ContentInfo::class)
            );
    }

    public function testValid()
    {
        $contentType = $this
                ->getMockBuilder(ContentType::class)
                ->setMethodsExcept(['__get'])
                ->setConstructorArgs([['isContainer' => true]])
                ->getMock();

        $this->contentTypeService->method('loadContentType')->willReturn($contentType);

        $this->executionContext
            ->expects($this->never())
            ->method('addViolation');

        $this->validator->validate($this->location, new LocationIsContainer());
    }

    public function testInvalid()
    {
        $contentType = $this
            ->getMockBuilder(ContentType::class)
            ->setMethodsExcept(['__get'])
            ->setConstructorArgs([['isContainer' => false]])
            ->getMock();

        $this->contentTypeService->method('loadContentType')->willReturn($contentType);

        $this->executionContext
            ->expects($this->once())
            ->method('addViolation');

        $this->validator->validate($this->location, new LocationIsContainer());
    }
}
