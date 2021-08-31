<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
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
    private const EXAMPLE_OBJECT_STATE_ID_A = 1;
    private const EXAMPLE_OBJECT_STATE_ID_B = 2;
    private const EXAMPLE_OBJECT_STATE_ID_C = 3;

    /** @var \eZ\Publish\API\Repository\ObjectStateService|\PHPUnit\Framework\MockObject\MockObject */
    private $objectStateService;

    /** @var \Psr\Log\LoggerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $logger;

    /** @var \EzSystems\EzPlatformAdminUi\Limitation\Mapper\ObjectStateLimitationMapper */
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
        $values = [
            self::EXAMPLE_OBJECT_STATE_ID_A,
            self::EXAMPLE_OBJECT_STATE_ID_B,
            self::EXAMPLE_OBJECT_STATE_ID_C,
        ];

        $expected = [
            $this->createStateMock('foo'),
            $this->createStateMock('bar'),
            $this->createStateMock('baz'),
        ];

        foreach ($values as $i => $value) {
            $this->objectStateService
                ->expects($this->at($i))
                ->method('loadObjectState')
                ->with($value)
                ->willReturn($expected[$i]);
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
        $this->objectStateService
            ->expects($this->once())
            ->method('loadObjectState')
            ->with(self::EXAMPLE_OBJECT_STATE_ID_A)
            ->willThrowException($this->createMock(NotFoundException::class));

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with('Could not map the Limitation value: could not find an Object state with ID ' . self::EXAMPLE_OBJECT_STATE_ID_A);

        $actual = $this->mapper->mapLimitationValue(new ObjectStateLimitation([
            'limitationValues' => [self::EXAMPLE_OBJECT_STATE_ID_A],
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
