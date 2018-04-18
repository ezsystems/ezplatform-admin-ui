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
use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\ConditionalTabInterface;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class RolesTab extends AbstractTab implements OrderedTabInterface, ConditionalTabInterface
{
    const URI_FRAGMENT = 'ez-tab-location-view-roles';

    /** @var \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory */
    protected $datasetFactory;

    /** @var array */
    private $userContentTypeIdentifier;

    /** @var array */
    private $userGroupContentTypeIdentifier;

    /**
     * @param \Twig\Environment $twig
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory $datasetFactory
     * @param array $userContentTypeIdentifier
     * @param array $userGroupContentTypeIdentifier
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        DatasetFactory $datasetFactory,
        array $userContentTypeIdentifier,
        array $userGroupContentTypeIdentifier
    ) {
        parent::__construct($twig, $translator);

        $this->datasetFactory = $datasetFactory;
        $this->userContentTypeIdentifier = $userContentTypeIdentifier;
        $this->userGroupContentTypeIdentifier = $userGroupContentTypeIdentifier;
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
     */
    public function evaluate(array $parameters): bool
    {
        /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType */
        $contentType = $parameters['contentType'];

        $isUser = new ContentTypeIsUser($this->userContentTypeIdentifier);
        $isUserGroup = new ContentTypeIsUserGroup($this->userGroupContentTypeIdentifier);
        $isUserOrUserGroup = (new OrSpecification($isUser, $isUserGroup))->isSatisfiedBy($contentType);

        return $isUserOrUserGroup;
    }

    /**
     * @param array $parameters
     *
     * @return string
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     * @throws \Twig_Error_Syntax
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Loader
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function renderView(array $parameters): string
    {
        /** @var \eZ\Publish\API\Repository\Values\Content\Location $location */
        $location = $parameters['location'];

        $rolesPaginationParams = $parameters['roles_pagination_params'];

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

        return $this->twig->render(
            'EzPlatformAdminUiBundle:content/tab/roles:tab.html.twig',
            array_merge($viewParameters, $parameters)
        );
    }
}
