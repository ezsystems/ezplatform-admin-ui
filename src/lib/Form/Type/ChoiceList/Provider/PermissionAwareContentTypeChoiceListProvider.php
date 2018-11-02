<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Provider;

use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Values\User\Limitation\ContentTypeLimitation;
use EzSystems\EzPlatformAdminUi\Util\PermissionUtilInterface;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;

class PermissionAwareContentTypeChoiceListProvider implements ChoiceListProviderInterface
{
    /** @var \EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Provider\ChoiceListProviderInterface */
    private $decorated;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \EzSystems\EzPlatformAdminUi\Util\PermissionUtilInterface */
    private $permissionUtil;

    /** @var string */
    private $module;

    /** @var string */
    private $function;

    /**
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \EzSystems\EzPlatformAdminUi\Util\PermissionUtilInterface $permissionUtil
     * @param \EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Provider\ContentTypeChoiceListProvider $decorated
     * @param string $module
     * @param string $function
     */
    public function __construct(
        PermissionResolver $permissionResolver,
        PermissionUtilInterface $permissionUtil,
        ContentTypeChoiceListProvider $decorated,
        string $module,
        string $function
    ) {
        $this->decorated = $decorated;
        $this->permissionResolver = $permissionResolver;
        $this->permissionUtil = $permissionUtil;
        $this->module = $module;
        $this->function = $function;
    }

    /**
     * @return array
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function getChoiceList(): array
    {
        $hasAccess = $this->permissionResolver->hasAccess($this->module, $this->function);
        if (!is_bool($hasAccess)) {
            $restrictedContentTypesIds = $this->permissionUtil->getRestrictions($hasAccess, ContentTypeLimitation::class);
        }

        $contentTypesGroups = $this->decorated->getChoiceList();

        if (empty($restrictedContentTypesIds)) {
            return $contentTypesGroups;
        }

        foreach ($contentTypesGroups as $group => $contentTypes) {
            $contentTypesGroups[$group] = array_filter($contentTypes, function (ContentType $contentType) use ($restrictedContentTypesIds) {
                return in_array($contentType->id, $restrictedContentTypesIds);
            });
        }

        return $contentTypesGroups;
    }
}
