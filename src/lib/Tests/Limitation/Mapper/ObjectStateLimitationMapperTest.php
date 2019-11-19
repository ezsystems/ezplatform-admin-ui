<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Limitation\Mapper;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\ObjectStateService;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup;
use eZ\Publish\API\Repository\Values\User\Limitation\ObjectStateLimitation;
use eZ\Publish\Core\Repository\Values\ObjectState\ObjectState;
use EzSystems\EzPlatformAdminUi\Limitation\Mapper\ObjectStateLimitationMapper;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ObjectStateLimitationMapperTest extends TestCase
{
    /** @var ObjectStateService|\PHPUnit\Framework\MockObject\MockObject */
    private $objectStateService;

    /** @var LoggerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $logger;

    /** @var ObjectStateLimitationMapper */
    private $mapper;

    protected function setUp(): void
    {
        $this->objectStateService = $this->createMock(ObjectStateService::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->mapper = new ObjectStateLimitationMapper($this->objectStateService);
        $this->mapper->setLogger($this->logger);
    }

    public function testMapLimitationValue()
    {
        $values = ['foo', 'bar', 'baz'];

        foreach ($values as $i => $value) {
            $stateMock = $this->createStateMock($value);

            $this->objectStateService
                ->expects($this->at($i))
                ->method('loadObjectState')
                ->with($value)
                ->willReturn($stateMock);
        }

        $result = $this->mapper->mapLimitationValue(new ObjectStateLimitation([
            'limitationValues' => $values,
        ]));

        $this->assertEquals([
            'foo:foo', 'bar:bar', 'baz:baz',
        ], $result);
    }

    public function testMapLimitationValueWithNotExistingObjectState()
    {
        $values = ['foo'];

        $this->objectStateService
            ->expects($this->once())
            ->method('loadObjectState')
            ->with($values[0])
            ->willThrowException($this->createMock(NotFoundException::class));

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with('Could not map limitation value: ObjectState with id = foo not found');

        $actual = $this->mapper->mapLimitationValue(new ObjectStateLimitation([
            'limitationValues' => $values,
        ]));

        $this->assertEmpty($actual);
    }

    private function createStateMock($value)
    {
        $stateGroupMock = $this->createMock(ObjectStateGroup::class);
        $stateGroupMock
            ->expects($this->once())
            ->method('getName')
            ->willReturn($value);

        $stateMock = $this->createMock(ObjectState::class);
        $stateMock
            ->expects($this->any())
            ->method('getObjectStateGroup')
            ->willReturn($stateGroupMock);

        $stateMock
            ->expects($this->any())
            ->method('getName')
            ->willReturn($value);

        return $stateMock;
    }
}
