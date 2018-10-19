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
use EzSystems\EzPlatformAdminUi\Util\PermissionUtil;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SectionAssign implements EventSubscriberInterface
{
    /** @var array */
    private $restrictedContentTypes;

    /** @var \EzSystems\EzPlatformAdminUi\Util\PermissionUtil */
    private $permissionUtil;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /**
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \EzSystems\EzPlatformAdminUi\Util\PermissionUtil $permissionUtil
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function __construct(
        PermissionResolver $permissionResolver,
        PermissionUtil $permissionUtil,
        ContentTypeService $contentTypeService
    ) {
        $this->permissionUtil = $permissionUtil;
        $this->contentTypeService = $contentTypeService;
        $hasAccess = $permissionResolver->hasAccess('section', 'assign');
        $this->restrictedContentTypes = is_array($hasAccess) ? $this->getRestrictedContentTypes($hasAccess) : [];
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
        if ('multiple' !== $configName) {
            return;
        }

        $context = $event->getContext();
        if (
            !isset($context['type'])
            || 'section_assign' !== $context['type']
        ) {
            return;
        }

        if ($this->hasContentTypeRestrictions()) {
            $config = $event->getConfig();
            $config['content_on_the_fly']['allowed_content_types'] = $this->restrictedContentTypes;
            $event->setConfig($config);
        }
    }

    /**
     * @param array $hasAccess
     *
     * @return array
     */
    private function getRestrictedContentTypes(array $hasAccess): array
    {
        $restrictedContentTypes = [];
        $restrictedContentTypesIds = [];

        foreach ($this->permissionUtil->flattenArrayOfLimitations($hasAccess) as $limitation) {
            if ($limitation instanceof ContentTypeLimitation) {
                $restrictedContentTypesIds[] = $limitation->limitationValues;
            }
        }

        if (empty($restrictedContentTypesIds)) {
            return $restrictedContentTypes;
        }

        $restrictedContentTypesIds = array_unique(array_merge(...$restrictedContentTypesIds));
        foreach ($restrictedContentTypesIds as $restrictedContentTypeId) {
            // TODO: Change to `contentTypeService->loadContentTypeList($restrictedContentTypesIds)` after #2444 will be merged
            try {
                $identifier = $this->contentTypeService->loadContentType($restrictedContentTypeId)->identifier;
                $restrictedContentTypes[] = $identifier;
            } catch (NotFoundException $e) {
            }
        }

        return $restrictedContentTypes;
    }

    /**
     * @return bool
     */
    private function hasContentTypeRestrictions(): bool
    {
        return !empty($this->restrictedContentTypes);
    }
}
