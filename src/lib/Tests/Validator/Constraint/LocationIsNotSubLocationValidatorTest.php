<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Validator\Constraint;

use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\LocationIsNotSubLocation;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\LocationIsNotSubLocationValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class LocationIsNotSubLocationValidatorTest extends TestCase
{
    /** @var \Symfony\Component\Validator\Context\ExecutionContextInterface */
    private $executionContext;

    /** @var \EzSystems\EzPlatformAdminUi\Validator\Constraints\LocationIsNotSubLocationValidator */
    private $validator;

    protected function setUp(): void
    {
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new LocationIsNotSubLocationValidator();
        $this->validator->initialize($this->executionContext);
    }

    public function testValid()
    {
        $location = $this
            ->getMockBuilder(Location::class)
            ->setMethodsExcept(['__get'])
            ->setConstructorArgs([['pathString' => '/1/2/3/']])
            ->getMock();

        $comparedLocation = $this
            ->getMockBuilder(Location::class)
            ->setMethodsExcept(['__get'])
            ->setConstructorArgs([['pathString' => '/3/5/']])
            ->getMock();

        $this->executionContext
            ->expects($this->never())
            ->method('addViolation');

        $constraint = new LocationIsNotSubLocation(['value' => $comparedLocation]);

        $this->validator->validate($location, $constraint);
    }

    public function testInvalid()
    {
        $location = $this
            ->getMockBuilder(Location::class)
            ->setMethodsExcept(['__get'])
            ->setConstructorArgs([['pathString' => '/1/2/3/']])
            ->getMock();

        $comparedLocation = $this
            ->getMockBuilder(Location::class)
            ->setMethodsExcept(['__get'])
            ->setConstructorArgs([['pathString' => '/1/2/']])
            ->getMock();

        $constraint = new LocationIsNotSubLocation(['value' => $comparedLocation]);

        $constraintViolationBuilder = $this
            ->getMockBuilder(ConstraintViolationBuilderInterface::class)
            ->getMock();

        $this->executionContext
            ->method('buildViolation')
            ->willReturn($constraintViolationBuilder);

        $constraintViolationBuilder
            ->method('setParameter')
            ->willReturn($constraintViolationBuilder);

        $constraintViolationBuilder
            ->method('setCode')
            ->willReturn($constraintViolationBuilder);

        $this->executionContext
            ->expects($this->once())
            ->method('buildViolation');

        $this->validator->validate($location, $constraint);
    }
}
