<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UniversalDiscovery\Event\Subscriber;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Values\User\Limitation\ContentTypeLimitation;
use EzSystems\EzPlatformAdminUi\UniversalDiscovery\Event\ConfigResolveEvent;
use EzSystems\EzPlatformAdminUi\Util\PermissionUtilInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ContentCreate implements EventSubscriberInterface
{
    /** @var array */
    private $restrictedContentTypesIdentifiers;

    /** @var \EzSystems\EzPlatformAdminUi\Util\PermissionUtilInterface */
    private $permissionUtil;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /**
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \EzSystems\EzPlatformAdminUi\Util\PermissionUtilInterface $permissionUtil
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function __construct(
        PermissionResolver $permissionResolver,
        PermissionUtilInterface $permissionUtil,
        ContentTypeService $contentTypeService
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->permissionUtil = $permissionUtil;

        $hasAccess = $permissionResolver->hasAccess('content', 'create');
        $this->restrictedContentTypesIdentifiers = $this->getRestrictedContentTypesIdentifiers($hasAccess);
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ConfigResolveEvent::NAME => ['onUdwConfigResolve'],
        ];
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\UniversalDiscovery\Event\ConfigResolveEvent $event
     */
    public function onUdwConfigResolve(ConfigResolveEvent $event): void
    {
        $configName = $event->getConfigName();
        if ('create' !== $configName) {
            return;
        }

        $context = $event->getContext();
        if (
            !isset($context['type'])
            || 'content_create' !== $context['type']
        ) {
            return;
        }

        if ($this->hasContentTypeRestrictions()) {
            $config = $event->getConfig();
            $config['content_on_the_fly']['allowed_content_types'] = $this->restrictedContentTypesIdentifiers;
            $event->setConfig($config);
        }
    }

    /**
     * @param array|bool $hasAccess
     *
     * @return array
     */
    private function getRestrictedContentTypesIdentifiers($hasAccess): array
    {
        if (!is_array($hasAccess)) {
            return [];
        }

        $restrictedContentTypesIds = $this->permissionUtil->getRestrictions($hasAccess, ContentTypeLimitation::class);

        if (empty($restrictedContentTypesIds)) {
            return [];
        }

        $restrictedContentTypesIdentifiers = [];
        foreach ($restrictedContentTypesIds as $restrictedContentTypeId) {
            // TODO: Change to `contentTypeService->loadContentTypeList($restrictedContentTypesIds)` after #2444 will be merged
            try {
                $identifier = $this->contentTypeService->loadContentType($restrictedContentTypeId)->identifier;
                $restrictedContentTypesIdentifiers[] = $identifier;
            } catch (NotFoundException $e) {
            }
        }

        return $restrictedContentTypesIdentifiers;
    }

    /**
     * @return bool
     */
    private function hasContentTypeRestrictions(): bool
    {
        return !empty($this->restrictedContentTypesIdentifiers);
    }
}
