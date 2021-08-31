<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Specification;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use EzSystems\EzPlatformAdminUi\Specification\Version\VersionHasConflict;
use PHPUnit\Framework\TestCase;

class VersionHasConflictTest extends TestCase
{
    public function testVersionWithStatusDraft()
    {
        /** @var \eZ\Publish\API\Repository\ContentService|\PHPUnit\Framework\MockObject\MockObject $contentServiceMock */
        $contentServiceMock = $this->createMock(ContentService::class);
        $contentServiceMock
            ->method('loadVersions')
            ->willReturn([
                $this->createVersionInfo(false),
                $this->createVersionInfo(false, 2),
                $this->createVersionInfo(false, 3),
                $this->createVersionInfo(true, 4),
            ]);

        $versionHasConflict = new VersionHasConflict($contentServiceMock, 'eng-GB');

        self::assertFalse($versionHasConflict->isSatisfiedBy($this->createVersionInfo(false, 5)));
    }

    public function testVersionWithStatusDraftAndVersionConflict()
    {
        /** @var \eZ\Publish\API\Repository\ContentService|\PHPUnit\Framework\MockObject\MockObject $contentServiceMock */
        $contentServiceMock = $this->createMock(ContentService::class);
        $contentServiceMock
            ->method('loadVersions')
            ->willReturn([
                $this->createVersionInfo(false),
                $this->createVersionInfo(false, 3),
                $this->createVersionInfo(true, 4),
            ]);

        $versionHasConflict = new VersionHasConflict($contentServiceMock, 'eng-GB');

        self::assertTrue($versionHasConflict->isSatisfiedBy($this->createVersionInfo(false, 2)));
    }

    public function testVersionWithStatusDraftAndVersionConflictWithAnotherLanguageCode()
    {
        $contentServiceMock = $this->createMock(ContentService::class);
        $contentServiceMock
            ->method('loadVersions')
            ->willReturn([
                $this->createVersionInfo(false, 1, 'pol-PL'),
                $this->createVersionInfo(false, 3, 'pol-PL'),
                $this->createVersionInfo(true, 4, 'pol-PL'),
            ]);

        $versionHasConflict = new VersionHasConflict($contentServiceMock, 'eng-GB');

        self::assertFalse($versionHasConflict->isSatisfiedBy($this->createVersionInfo(false, 2, 'eng-GB')));
    }

    /**
     * Returns VersionInfo.
     *
     * @param bool $isPublished
     * @param int $versionNo
     * @param string $languageCode
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\eZ\Publish\API\Repository\Values\Content\VersionInfo
     */
    private function createVersionInfo(bool $isPublished = false, int $versionNo = 1, string $languageCode = 'eng-GB'): VersionInfo
    {
        $contentInfo = $this->createMock(ContentInfo::class);

        $versionInfo = $this->getMockForAbstractClass(
            VersionInfo::class,
            [],
            '',
            true,
            true,
            true,
            ['isPublished', '__get', 'getContentInfo']
        );

        $versionInfo
            ->method('isPublished')
            ->willReturn($isPublished);

        $versionInfo
            ->method('__get')
            ->willReturnMap(
                [
                    ['initialLanguageCode', $languageCode],
                    ['versionNo', $versionNo],
                ]
            );

        $versionInfo
            ->method('getContentInfo')
            ->willReturn($contentInfo);

        return $versionInfo;
    }
}
