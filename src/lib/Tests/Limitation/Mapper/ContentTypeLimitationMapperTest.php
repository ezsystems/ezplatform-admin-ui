<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Limitation\Mapper;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\User\Limitation\ContentTypeLimitation;
use EzSystems\EzPlatformAdminUi\Limitation\Mapper\ContentTypeLimitationMapper;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ContentTypeLimitationMapperTest extends TestCase
{
    private const EXAMPLE_CONTENT_TYPE_ID_A = 1;
    private const EXAMPLE_CONTENT_TYPE_ID_B = 2;
    private const EXAMPLE_CONTENT_TYPE_ID_C = 3;

    /** @var \eZ\Publish\API\Repository\ContentTypeService|\PHPUnit\Framework\MockObject\MockObject */
    private $contentTypeService;

    /** @var \Psr\Log\LoggerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $logger;

    /** @var \EzSystems\EzPlatformAdminUi\Limitation\Mapper\ContentTypeLimitationMapper */
    private $mapper;

    protected function setUp(): void
    {
        $this->contentTypeService = $this->createMock(ContentTypeService::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->mapper = new ContentTypeLimitationMapper($this->contentTypeService);
        $this->mapper->setLogger($this->logger);
    }

    public function testMapLimitationValue()
    {
        $values = [
            self::EXAMPLE_CONTENT_TYPE_ID_A,
            self::EXAMPLE_CONTENT_TYPE_ID_B,
            self::EXAMPLE_CONTENT_TYPE_ID_C,
        ];

        $expected = [
            $this->createMock(ContentType::class),
            $this->createMock(ContentType::class),
            $this->createMock(ContentType::class),
        ];

        foreach ($values as $i => $value) {
            $this->contentTypeService
                ->expects($this->at($i))
                ->method('loadContentType')
                ->with($value)
                ->willReturn($expected[$i]);
        }

        $result = $this->mapper->mapLimitationValue(new ContentTypeLimitation([
            'limitationValues' => $values,
        ]));

        $this->assertEquals($expected, $result);
        $this->assertCount(3, $result);
    }

    public function testMapLimitationValueWithNotExistingContentType()
    {
        $this->contentTypeService
            ->expects($this->once())
            ->method('loadContentType')
            ->with(self::EXAMPLE_CONTENT_TYPE_ID_A)
            ->willThrowException($this->createMock(NotFoundException::class));

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with('Could not map the Limitation value: could not find a Content Type with ID ' . self::EXAMPLE_CONTENT_TYPE_ID_A);

        $actual = $this->mapper->mapLimitationValue(new ContentTypeLimitation([
            'limitationValues' => [self::EXAMPLE_CONTENT_TYPE_ID_A],
        ]));

        $this->assertEmpty($actual);
    }
}
