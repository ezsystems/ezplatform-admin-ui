<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Limitation\Mapper;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\User\Limitation\LanguageLimitation;
use EzSystems\EzPlatformAdminUi\Limitation\Mapper\LanguageLimitationMapper;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class LanguageTypeLimitationMapperTest extends TestCase
{
    /** @var \eZ\Publish\API\Repository\LanguageService|\PHPUnit\Framework\MockObject\MockObject */
    private $languageService;

    /** @var \Psr\Log\LoggerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $logger;

    /** @var \EzSystems\EzPlatformAdminUi\Limitation\Mapper\LanguageLimitationMapper */
    private $mapper;

    protected function setUp(): void
    {
        $this->languageService = $this->createMock(LanguageService::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->mapper = new LanguageLimitationMapper($this->languageService);
        $this->mapper->setLogger($this->logger);
    }

    public function testMapLimitationValue()
    {
        $values = ['en_GB', 'en_US', 'pl_PL'];

        $expected = [
            $this->createMock(Language::class),
            $this->createMock(Language::class),
            $this->createMock(Language::class),
        ];

        foreach ($values as $i => $value) {
            $this->languageService
                ->expects($this->at($i))
                ->method('loadLanguage')
                ->with($value)
                ->willReturn($expected[$i]);
        }

        $result = $this->mapper->mapLimitationValue(new LanguageLimitation([
            'limitationValues' => $values,
        ]));

        $this->assertEquals($expected, $result);
    }

    public function testMapLimitationValueWithNotExistingContentType()
    {
        $values = ['foo'];

        $this->languageService
            ->expects($this->once())
            ->method('loadLanguage')
            ->with($values[0])
            ->willThrowException($this->createMock(NotFoundException::class));

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with('Could not map the Limitation value: could not find a language with code foo');

        $actual = $this->mapper->mapLimitationValue(new LanguageLimitation([
            'limitationValues' => $values,
        ]));

        $this->assertEmpty($actual);
    }
}
