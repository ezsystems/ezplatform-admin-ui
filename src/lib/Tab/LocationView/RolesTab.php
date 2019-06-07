<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\LocationView;

use EzSystems\EzPlatformAdminUi\Specification\ContentType\ContentTypeIsUser;
use EzSystems\EzPlatformAdminUi\Specification\ContentType\ContentTypeIsUserGroup;
use EzSystems\EzPlatformAdminUi\Specification\OrSpecification;
use EzSystems\EzPlatformAdminUi\Tab\ConditionalTabInterface;
use EzSystems\EzPlatformAdminUi\Tab\AbstractEventDispatchingTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use eZ\Publish\API\Repository\PermissionResolver;

class RolesTab extends AbstractEventDispatchingTab implements OrderedTabInterface, ConditionalTabInterface
{
    const URI_FRAGMENT = 'ez-tab-location-view-roles';

    /** @var \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory */
    protected $datasetFactory;

    /** @var array */
    private $userContentTypeIdentifier;

    /** @var array */
    private $userGroupContentTypeIdentifier;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    protected $permissionResolver;

    /**
     * @param \Twig\Environment $twig
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     * @param \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory $datasetFactory
     * @param array $userContentTypeIdentifier
     * @param array $userGroupContentTypeIdentifier
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        DatasetFactory $datasetFactory,
        array $userContentTypeIdentifier,
        array $userGroupContentTypeIdentifier,
        PermissionResolver $permissionResolver,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($twig, $translator, $eventDispatcher);

        $this->datasetFactory = $datasetFactory;
        $this->userContentTypeIdentifier = $userContentTypeIdentifier;
        $this->userGroupContentTypeIdentifier = $userGroupContentTypeIdentifier;
        $this->permissionResolver = $permissionResolver;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'roles';
    }

    /**
     * @return string
     *
     * @throws \Symfony\Component\Translation\Exception\InvalidArgumentException
     */
    public function getName(): string
    {
        /** @Desc("Roles") */
        return $this->translator->trans('tab.name.roles', [], 'locationview');
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return 800;
    }

    /**
     * Get information about tab presence.
     *
     * @param array $parameters
     *
     * @return bool
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function evaluate(array $parameters): bool
    {
        if (false === $this->permissionResolver->canUser('role', 'read', $parameters['content'])) {
            return false;
        }

        /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType */
        $contentType = $parameters['contentType'];

        $isUser = new ContentTypeIsUser($this->userContentTypeIdentifier);
        $isUserGroup = new ContentTypeIsUserGroup($this->userGroupContentTypeIdentifier);
        $isUserOrUserGroup = (new OrSpecification($isUser, $isUserGroup))->isSatisfiedBy($contentType);

        return $isUserOrUserGroup;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate(): string
    {
        return '@ezdesign/content/tab/roles/tab.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateParameters(array $contextParameters = []): array
    {
        /** @var \eZ\Publish\API\Repository\Values\Content\Location $location */
        $location = $contextParameters['location'];

        $rolesPaginationParams = $contextParameters['roles_pagination_params'];

        $rolesDataset = $this->datasetFactory->roles();
        $rolesDataset->load($location);

        $rolesPagerfanta = new Pagerfanta(
            new ArrayAdapter($rolesDataset->getRoles())
        );

        $rolesPagerfanta->setMaxPerPage($rolesPaginationParams['limit']);
        $rolesPagerfanta->setCurrentPage(min($rolesPaginationParams['page'], $rolesPagerfanta->getNbPages()));

        $viewParameters = [
            'roles_pager' => $rolesPagerfanta,
            'roles_pagination_params' => $rolesPaginationParams,
        ];

        return array_replace($contextParameters, $viewParameters);
    }
}
