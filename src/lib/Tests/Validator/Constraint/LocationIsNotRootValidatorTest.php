<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Validator\Constraint;

use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\LocationIsNotRoot;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\LocationIsNotRootValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class LocationIsNotRootValidatorTest extends TestCase
{
    /** @var \Symfony\Component\Validator\Context\ExecutionContextInterface */
    private $executionContext;

    /** @var \EzSystems\EzPlatformAdminUi\Validator\Constraints\LocationIsContainerValidator */
    private $validator;

    protected function setUp(): void
    {
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new LocationIsNotRootValidator();
        $this->validator->initialize($this->executionContext);
    }

    public function testValid()
    {
        $location = $this
            ->getMockBuilder(Location::class)
            ->setMethodsExcept(['__get'])
            ->setConstructorArgs([['depth' => 5]])
            ->getMock();

        $this->executionContext
            ->expects($this->never())
            ->method('addViolation');

        $this->validator->validate($location, new LocationIsNotRoot());
    }

    public function testInvalid()
    {
        $location = $this
            ->getMockBuilder(Location::class)
            ->setMethodsExcept(['__get'])
            ->setConstructorArgs([['depth' => 1]])
            ->getMock();

        $this->executionContext
            ->expects($this->once())
            ->method('addViolation');

        $this->validator->validate($location, new LocationIsNotRoot());
    }
}
