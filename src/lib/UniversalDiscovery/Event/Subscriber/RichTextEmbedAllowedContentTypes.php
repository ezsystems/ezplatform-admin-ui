<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UniversalDiscovery\Event\Subscriber;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\User\Limitation\ContentTypeLimitation;
use EzSystems\EzPlatformAdminUi\Permission\PermissionCheckerInterface;
use EzSystems\EzPlatformAdminUi\UniversalDiscovery\Event\ConfigResolveEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RichTextEmbedAllowedContentTypes implements EventSubscriberInterface
{
    /** @var string[] */
    private $allowedContentTypesIdentifiers;

    /**
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \EzSystems\EzPlatformAdminUi\Permission\PermissionCheckerInterface $permissionChecker
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function __construct(
        PermissionResolver $permissionResolver,
        PermissionCheckerInterface $permissionChecker,
        ContentTypeService $contentTypeService
    ) {
        $this->allowedContentTypesIdentifiers = $this->getAllowedContentTypesIdentifiers(
            $permissionResolver,
            $permissionChecker,
            $contentTypeService
        );
    }

    /**
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \EzSystems\EzPlatformAdminUi\Permission\PermissionCheckerInterface $permissionChecker
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    private function getAllowedContentTypesIdentifiers(
        PermissionResolver $permissionResolver,
        PermissionCheckerInterface $permissionChecker,
        ContentTypeService $contentTypeService
    ): array {
        $access = $permissionResolver->hasAccess('content', 'read');
        if (!\is_array($access)) {
            return [];
        }

        $restrictedContentTypesIds = $permissionChecker->getRestrictions($access, ContentTypeLimitation::class);

        if (empty($restrictedContentTypesIds)) {
            return [];
        }

        $restrictedContentTypes = $contentTypeService->loadContentTypeList($restrictedContentTypesIds);

        return array_values(array_map(function (ContentType $contentType): string {
            return $contentType->identifier;
        }, (array)$restrictedContentTypes));
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ConfigResolveEvent::NAME => ['onUdwConfigResolve', -10],
        ];
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\UniversalDiscovery\Event\ConfigResolveEvent $event
     */
    public function onUdwConfigResolve(ConfigResolveEvent $event): void
    {
        $config = $event->getConfig();

        if (!in_array($event->getConfigName(), ['richtext_embed', 'richtext_embed_image'])) {
            return;
        }

        $config['allowed_content_types'] = !empty($this->allowedContentTypesIdentifiers) ? $this->restrictedContentTypesIdentifier : null;

        $event->setConfig($config);
    }
}
