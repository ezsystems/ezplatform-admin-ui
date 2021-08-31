<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Validator\Constraint;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\LocationIsContainer;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\LocationIsContainerValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class LocationIsContainerValidatorTest extends TestCase
{
    /** @var \Symfony\Component\Validator\Context\ExecutionContextInterface */
    private $executionContext;

    /** @var \EzSystems\EzPlatformAdminUi\Validator\Constraints\LocationIsContainerValidator */
    private $validator;

    /** @var \eZ\Publish\API\Repository\Values\Content\Location|\PHPUnit\Framework\MockObject\MockObject */
    private $location;

    /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType|\PHPUnit\Framework\MockObject\MockObject */
    private $contentType;

    protected function setUp(): void
    {
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new LocationIsContainerValidator();
        $this->validator->initialize($this->executionContext);

        $content = $this->createMock(Content::class);

        $this->location = $this->createMock(Location::class);
        $this->location
            ->method('getContent')
            ->willReturn($content);

        $this->contentType = $this->createMock(ContentType::class);

        $content
            ->method('getContentType')
            ->willReturn($this->contentType);
    }

    public function testValid()
    {
        $this->contentType
            ->method('__get')
            ->with('isContainer')
            ->willReturn(true);

        $this->executionContext
            ->expects($this->never())
            ->method('addViolation');

        $this->validator->validate($this->location, new LocationIsContainer());
    }

    public function testInvalid()
    {
        $this->contentType
            ->method('__get')
            ->with('isContainer')
            ->willReturn(false);

        $this->executionContext
            ->expects($this->once())
            ->method('addViolation');

        $this->validator->validate($this->location, new LocationIsContainer());
    }
}
