<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Specification;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use EzSystems\EzPlatformAdminUi\Specification\Version\VersionHasConflict;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;

class VersionHasConflictTest extends TestCase
{
    public function testVersionWithStatusDraft()
    {
        /** @var ContentService|MockObject $contentServiceMock */
        $contentServiceMock = $this->createMock(ContentService::class);
        $contentServiceMock
            ->method('loadVersions')
            ->willReturn([
                $this->createVersionInfo(false),
                $this->createVersionInfo(false, 2),
                $this->createVersionInfo(false, 3),
                $this->createVersionInfo(true, 4),
            ]);

        $versionHasConflict = new VersionHasConflict($contentServiceMock);

        self::assertFalse($versionHasConflict->isSatisfiedBy($this->createVersionInfo(false, 5)));
    }

    public function testVersionWithStatusDraftAndVersionConflict()
    {
        /** @var ContentService|MockObject $contentServiceMock */
        $contentServiceMock = $this->createMock(ContentService::class);
        $contentServiceMock
            ->method('loadVersions')
            ->willReturn([
                $this->createVersionInfo(false),
                $this->createVersionInfo(false, 3),
                $this->createVersionInfo(true, 4),
            ]);

        $versionHasConflict = new VersionHasConflict($contentServiceMock);

        self::assertTrue($versionHasConflict->isSatisfiedBy($this->createVersionInfo(false, 2)));
    }

    /**
     * Returns VersionInfo.
     *
     * @param bool $isPublished
     * @param int $versionNo
     *
     * @return VersionInfo|MockObject
     */
    private function createVersionInfo(bool $isPublished = false, int $versionNo = 1): VersionInfo
    {
        $contentInfo = $this->createMock(ContentInfo::class);

        $versionInfo = $this->getMockForAbstractClass(VersionInfo::class, [], '', true, true, true, ['isPublished', '__get', 'getContentInfo']);
        $versionInfo->method('isPublished')->willReturn($isPublished);
        $versionInfo->method('__get')->with($this->equalTo('versionNo'))->willReturn($versionNo);
        $versionInfo->method('getContentInfo')->willReturn($contentInfo);

        return $versionInfo;
    }
}
