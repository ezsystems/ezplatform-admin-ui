<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\UniversalDiscovery\Event\Subscriber;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\User\Limitation\ContentTypeLimitation;
use EzSystems\EzPlatformAdminUi\Permission\PermissionCheckerInterface;
use EzSystems\EzPlatformAdminUi\UniversalDiscovery\Event\ConfigResolveEvent;
use EzSystems\EzPlatformAdminUi\UniversalDiscovery\Event\Subscriber\RichTextEmbedAllowedContentTypes;
use PHPUnit\Framework\TestCase;

final class RichTextEmbedAllowedContentTypesTest extends TestCase
{
    private const EXAMPLE_LIMITATIONS = [/* Some limitations */];

    private const SUPPORTED_CONFIG_NAMES = ['richtext_embed', 'richtext_embed_image'];

    private const ALLOWED_CONTENT_TYPES_IDS = [2, 4];
    private const ALLOWED_CONTENT_TYPES = ['article', 'folder'];

    /** @var \eZ\Publish\API\Repository\PermissionResolver|\PHPUnit\Framework\MockObject\MockObject */
    private $permissionResolver;

    /** @var \EzSystems\EzPlatformAdminUi\Permission\PermissionCheckerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $permissionChecker;

    /** @var \eZ\Publish\API\Repository\ContentTypeService|\PHPUnit\Framework\MockObject\MockObject */
    private $contentTypeService;

    /** @var \EzSystems\EzPlatformAdminUi\UniversalDiscovery\Event\Subscriber\RichTextEmbedAllowedContentTypes */
    private $subscriber;

    protected function setUp(): void
    {
        $this->permissionResolver = $this->createMock(PermissionResolver::class);
        $this->permissionChecker = $this->createMock(PermissionCheckerInterface::class);
        $this->contentTypeService = $this->createMock(ContentTypeService::class);

        $this->subscriber = new RichTextEmbedAllowedContentTypes(
            $this->permissionResolver,
            $this->permissionChecker,
            $this->contentTypeService
        );
    }

    public function testUdwConfigResolveOnUnsupportedConfigName(): void
    {
        $this->permissionResolver->expects($this->never())->method('hasAccess');
        $this->permissionChecker->expects($this->never())->method('getRestrictions');
        $this->contentTypeService->expects($this->never())->method('loadContentTypeList');

        $event = $this->createConfigResolveEvent('unsupported_config_name');

        $this->subscriber->onUdwConfigResolve($event);

        $this->assertEquals([], $event->getConfig());
    }

    public function testUdwConfigResolveWhenThereIsNoContentReadLimitations(): void
    {
        $this->permissionResolver->method('hasAccess')->with('content', 'read')->willReturn(true);
        $this->permissionChecker->expects($this->never())->method('getRestrictions');
        $this->contentTypeService->expects($this->never())->method('loadContentTypeList');

        $this->assertConfigurationResolvingResult([
            'allowed_content_types' => null,
        ]);
    }

    public function testUdwConfigResolveWhenThereIsNoContentReadLimitationsAndNoAccess(): void
    {
        $this->permissionResolver->method('hasAccess')->with('content', 'read')->willReturn(false);
        $this->permissionChecker->expects($this->never())->method('getRestrictions');
        $this->contentTypeService->expects($this->never())->method('loadContentTypeList');

        $this->assertConfigurationResolvingResult([
            'allowed_content_types' => [null],
        ]);
    }

    public function testUdwConfigResolveWhenThereAreContentReadLimitations(): void
    {
        $this->permissionResolver
            ->method('hasAccess')
            ->with('content', 'read')
            ->willReturn(self::EXAMPLE_LIMITATIONS);

        $this->permissionChecker
            ->method('getRestrictions')
            ->with(self::EXAMPLE_LIMITATIONS, ContentTypeLimitation::class)
            ->willReturn(self::ALLOWED_CONTENT_TYPES_IDS);

        $this->contentTypeService
            ->method('loadContentTypeList')
            ->with(self::ALLOWED_CONTENT_TYPES_IDS)
            ->willReturn($this->createContentTypeListMock(self::ALLOWED_CONTENT_TYPES));

        $this->assertConfigurationResolvingResult([
            'allowed_content_types' => self::ALLOWED_CONTENT_TYPES,
        ]);
    }

    private function assertConfigurationResolvingResult(?array $expectedConfiguration): void
    {
        foreach (self::SUPPORTED_CONFIG_NAMES as $configName) {
            $event = $this->createConfigResolveEvent($configName);

            $this->subscriber->onUdwConfigResolve($event);

            $this->assertEquals(
                $expectedConfiguration,
                $event->getConfig()
            );
        }
    }

    private function createConfigResolveEvent(string $configName = 'richtext_embed'): ConfigResolveEvent
    {
        $event = new ConfigResolveEvent();
        $event->setConfigName($configName);

        return $event;
    }

    private function createContentTypeListMock(array $identifiers): array
    {
        return array_map(function (string $identifier) {
            $contentType = $this->createMock(ContentType::class);
            $contentType->method('__get')->with('identifier')->willReturn($identifier);

            return $contentType;
        }, $identifiers);
    }
}
