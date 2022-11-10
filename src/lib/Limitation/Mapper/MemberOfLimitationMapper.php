<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Limitation\Mapper;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\ContentName;
use eZ\Publish\API\Repository\Values\Filter\Filter;
use eZ\Publish\API\Repository\Values\User\Limitation;
use EzSystems\EzPlatformAdminUi\Limitation\LimitationValueMapperInterface;
use EzSystems\EzPlatformAdminUi\Limitation\Mapper\MultipleSelectionBasedMapper;
use Ibexa\Core\Limitation\MemberOfLimitationType;
use Symfony\Contracts\Translation\TranslatorInterface;

final class MemberOfLimitationMapper extends MultipleSelectionBasedMapper implements LimitationValueMapperInterface
{
    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    public function __construct(
        UserService $userService,
        Repository $repository,
        ContentService $contentService,
        TranslatorInterface $translator
    ) {
        $this->userService = $userService;
        $this->repository = $repository;
        $this->contentService = $contentService;
        $this->translator = $translator;
    }

    protected function getSelectionChoices(): array
    {
        $userGroups = $this->loadUserGroups();
        $choices = [];
        $choices[MemberOfLimitationType::SELF_USER_GROUP] = $this->getSelfUserGroupLabel();

        foreach ($userGroups as $userGroup) {
            $choices[$userGroup->id] = $userGroup->getName();
        }

        return $choices;
    }

    public function mapLimitationValue(Limitation $limitation): array
    {
        $values = [];
        foreach ($limitation->limitationValues as $groupId) {
            if ((int)$groupId === MemberOfLimitationType::SELF_USER_GROUP) {
                $values[] = $this->getSelfUserGroupLabel();
                continue;
            }
            $values[] = $this->userService->loadUserGroup((int)$groupId)->getName();
        }

        return $values;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\User\UserGroup[]
     */
    private function loadUserGroups(): array
    {
        return $this->repository->sudo(function () {
            $filter = new Filter();
            $filter->withCriterion(new ContentTypeIdentifier('user_group'));
            $filter->withSortClause(new ContentName());
            $results = $this->contentService->find($filter);

            $groups = [];
            foreach ($results as $result) {
                $groups[] = $this->userService->loadUserGroup($result->id);
            }

            return $groups;
        });
    }

    private function getSelfUserGroupLabel(): string
    {
        return $this->translator->trans(
            /** @Desc("Self") */
            'policy.limitation.member_of.self_user_group',
            [],
            'ezplatform_content_forms_role'
        );
    }
}
