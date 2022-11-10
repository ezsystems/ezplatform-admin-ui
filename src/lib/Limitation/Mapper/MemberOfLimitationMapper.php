<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Limitation\Mapper;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\ContentName;
use eZ\Publish\API\Repository\Values\User\Limitation;
use EzSystems\RepositoryForms\Limitation\LimitationValueMapperInterface;
use EzSystems\RepositoryForms\Limitation\Mapper\MultipleSelectionBasedMapper;
use Ibexa\Core\Limitation\MemberOfLimitationType;
use Symfony\Component\Translation\TranslatorInterface;

final class MemberOfLimitationMapper extends MultipleSelectionBasedMapper implements LimitationValueMapperInterface
{
    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    /** @var \eZ\Publish\API\Repository\SearchService */
    private $searchService;

    /** @var \Symfony\Component\Translation\TranslatorInterface */
    private $translator;

    public function __construct(
        UserService $userService,
        Repository $repository,
        SearchService $searchService,
        TranslatorInterface $translator
    ) {
        $this->userService = $userService;
        $this->repository = $repository;
        $this->searchService = $searchService;
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
            if ($groupId === MemberOfLimitationType::SELF_USER_GROUP) {
                $values[] = $this->getSelfUserGroupLabel();
                continue;
            }
            $values[] = $this->userService->loadUserGroup($groupId)->getName();
        }

        return $values;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\User\UserGroup[]
     */
    private function loadUserGroups(): array
    {
        return $this->repository->sudo(function () {
            $query = new Query();
            $query->filter = new ContentTypeIdentifier('user_group');
            $query->offset = 0;
            $query->limit = 100;
            $query->performCount = true;
            $query->sortClauses[] = new ContentName();

            $groups = [];
            do {
                $results = $this->searchService->findContent($query);
                foreach ($results->searchHits as $hit) {
                    $groups[] = $this->userService->loadUserGroup($hit->valueObject->id);
                }

                $query->offset += $query->limit;
            } while ($query->offset < $results->totalCount);

            return $groups;
        });
    }

    private function getSelfUserGroupLabel(): string
    {
        return $this->translator->trans(
            /** @Desc("Self") */
            'policy.limitation.member_of.self_user_group',
            [],
            'ezrepoforms_role'
        );
    }
}
