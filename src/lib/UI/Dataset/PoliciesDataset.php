<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Dataset;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\User\Policy;
use EzSystems\EzPlatformAdminUi\Specification\ContentType\ContentTypeIsUser;
use EzSystems\EzPlatformAdminUi\Specification\ContentType\ContentTypeIsUserGroup;
use EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory;

class PoliciesDataset
{
    /** @var \eZ\Publish\API\Repository\RoleService */
    private $roleService;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory */
    protected $valueFactory;

    /** @var array */
    private $userContentTypeIdentifier;

    /** @var array */
    private $userGroupContentTypeIdentifier;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Value\Content\UrlAlias[] */
    private $data;

    /**
     * @param \eZ\Publish\API\Repository\RoleService $roleService
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \eZ\Publish\API\Repository\UserService $userService
     * @param \EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory $valueFactory
     * @param array $userContentTypeIdentifier
     * @param array $userGroupContentTypeIdentifier
     */
    public function __construct(
        RoleService $roleService,
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        UserService $userService,
        ValueFactory $valueFactory,
        array $userContentTypeIdentifier,
        array $userGroupContentTypeIdentifier
    ) {
        $this->roleService = $roleService;
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->userService = $userService;
        $this->valueFactory = $valueFactory;
        $this->userContentTypeIdentifier = $userContentTypeIdentifier;
        $this->userGroupContentTypeIdentifier = $userGroupContentTypeIdentifier;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return \EzSystems\EzPlatformAdminUi\UI\Dataset\PoliciesDataset
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function load(Location $location): self
    {
        $roleAssignments = [];
        $content = $location->getContent();
        $contentType = $content->getContentType();

        if ((new ContentTypeIsUser($this->userContentTypeIdentifier))->isSatisfiedBy($contentType)) {
            $user = $this->userService->loadUser($content->id);
            $roleAssignments = $this->roleService->getRoleAssignmentsForUser($user, true);
        }

        if ((new ContentTypeIsUserGroup($this->userGroupContentTypeIdentifier))->isSatisfiedBy($contentType)) {
            $userGroup = $this->userService->loadUserGroup($content->id);
            $roleAssignments = $this->roleService->getRoleAssignmentsForUserGroup($userGroup);
        }

        $this->data = [];
        $policies = [[]];

        foreach ($roleAssignments as $roleAssignment) {
            $policies[] = array_map(
                function (Policy $policy) use ($roleAssignment) {
                    return $this->valueFactory->createPolicy($policy, $roleAssignment);
                },
                $roleAssignment->getRole()->getPolicies()
            );
        }

        $this->data = array_merge(...$policies);

        return $this;
    }

    /**
     * @return \EzSystems\EzPlatformAdminUi\UI\Value\User\Policy[]
     */
    public function getPolicies(): array
    {
        return $this->data;
    }
}
