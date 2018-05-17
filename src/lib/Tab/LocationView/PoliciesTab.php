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
use eZ\Publish\API\Repository\PermissionResolver;

class PoliciesTab extends AbstractTab implements OrderedTabInterface, ConditionalTabInterface
{
    const URI_FRAGMENT = 'ez-tab-location-view-policies';

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
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory $datasetFactory
     * @param array $userContentTypeIdentifier
     * @param array $userGroupContentTypeIdentifier
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        DatasetFactory $datasetFactory,
        array $userContentTypeIdentifier,
        array $userGroupContentTypeIdentifier,
        PermissionResolver $permissionResolver
    ) {
        parent::__construct($twig, $translator);

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
        return 'policies';
    }

    /**
     * @return string
     *
     * @throws \Symfony\Component\Translation\Exception\InvalidArgumentException
     */
    public function getName(): string
    {
        /** @Desc("Policies") */
        return $this->translator->trans('tab.name.policies', [], 'locationview');
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return 900;
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

        $policiesPaginationParams = $parameters['policies_pagination_params'];

        $policiesDataset = $this->datasetFactory->policies();
        $policiesDataset->load($location);

        $policiesPagerfanta = new Pagerfanta(
            new ArrayAdapter($policiesDataset->getPolicies())
        );

        $policiesPagerfanta->setMaxPerPage($policiesPaginationParams['limit']);
        $policiesPagerfanta->setCurrentPage(min($policiesPaginationParams['page'], $policiesPagerfanta->getNbPages()));

        $viewParameters = [
            'policies_pager' => $policiesPagerfanta,
            'policies_pagination_params' => $policiesPaginationParams,
        ];

        return $this->twig->render(
            'EzPlatformAdminUiBundle:content/tab/policies:tab.html.twig',
            array_merge($viewParameters, $parameters)
        );
    }
}
