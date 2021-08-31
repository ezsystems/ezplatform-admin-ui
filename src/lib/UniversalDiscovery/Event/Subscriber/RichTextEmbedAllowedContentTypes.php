<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UniversalDiscovery\Event\Subscriber;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Values\User\Limitation\ContentTypeLimitation;
use EzSystems\EzPlatformAdminUi\Permission\PermissionCheckerInterface;
use EzSystems\EzPlatformAdminUi\UniversalDiscovery\Event\ConfigResolveEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class RichTextEmbedAllowedContentTypes implements EventSubscriberInterface
{
    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \EzSystems\EzPlatformAdminUi\Permission\PermissionCheckerInterface */
    private $permissionChecker;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var string[]|null */
    private $allowedContentTypesIdentifiers = null;

    public function __construct(
        PermissionResolver $permissionResolver,
        PermissionCheckerInterface $permissionChecker,
        ContentTypeService $contentTypeService
    ) {
        $this->permissionResolver = $permissionResolver;
        $this->permissionChecker = $permissionChecker;
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    private function getAllowedContentTypesIdentifiers(array $contentTypesAllowedViaConfig): ?array
    {
        $access = $this->permissionResolver->hasAccess('content', 'read');
        if (!\is_array($access)) {
            return $access ? ($contentTypesAllowedViaConfig ?: null) : [null];
        }

        $restrictedContentTypesIds = $this->permissionChecker->getRestrictions($access, ContentTypeLimitation::class);
        if (empty($restrictedContentTypesIds)) {
            return $contentTypesAllowedViaConfig ?: null;
        }

        $allowedContentTypesIdentifiers = [];

        $restrictedContentTypes = $this->contentTypeService->loadContentTypeList($restrictedContentTypesIds);
        foreach ($restrictedContentTypes as $contentType) {
            $allowedContentTypesIdentifiers[] = $contentType->identifier;
        }

        $allowedContentTypesIdentifiers = count($contentTypesAllowedViaConfig)
            ? array_intersect($contentTypesAllowedViaConfig, $allowedContentTypesIdentifiers)
            : $allowedContentTypesIdentifiers;

        return empty($allowedContentTypesIdentifiers) ? [null] : array_values($allowedContentTypesIdentifiers);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConfigResolveEvent::NAME => ['onUdwConfigResolve', -10],
        ];
    }

    public function onUdwConfigResolve(ConfigResolveEvent $event): void
    {
        $config = $event->getConfig();

        if (!in_array($event->getConfigName(), ['richtext_embed', 'richtext_embed_image'])) {
            return;
        }

        if ($this->allowedContentTypesIdentifiers === null) {
            $this->allowedContentTypesIdentifiers = $this->getAllowedContentTypesIdentifiers($config['allowed_content_types'] ?? []);
        }

        $config['allowed_content_types'] = $this->allowedContentTypesIdentifiers;

        $event->setConfig($config);
    }
}
