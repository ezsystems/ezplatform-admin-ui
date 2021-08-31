<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserChoiceType extends AbstractType
{
    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    /**
     * UserGroupChoiceType constructor.
     *
     * @param \eZ\Publish\API\Repository\Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choice_loader' => new CallbackChoiceLoader(function () {
                return $this->getUsers();
            }),
            'choice_label' => 'name',
            'choice_value' => 'id',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getParent(): ?string
    {
        return ChoiceType::class;
    }

    /**
     * Get users list.
     *
     * @return \eZ\Publish\API\Repository\Values\User\User[]
     */
    protected function getUsers(): array
    {
        return $this->repository->sudo(static function (Repository $repository) {
            $query = new LocationQuery();
            $query->filter = new ContentTypeIdentifier('user');
            $query->offset = 0;
            $query->limit = 10;
            $query->performCount = true;
            $query->sortClauses[] = new SortClause\ContentName();

            $users = [];
            do {
                $results = $repository->getSearchService()->findContent($query);
                foreach ($results->searchHits as $hit) {
                    $users[] = $repository->sudo(static function (Repository $repository) use ($hit) {
                        return $repository->getUserService()->loadUser($hit->valueObject->id);
                    });
                }

                $query->offset += $query->limit;
            } while ($query->offset < $results->totalCount);

            return $users;
        });
    }
}
