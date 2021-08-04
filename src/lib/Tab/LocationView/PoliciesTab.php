<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Tab\LocationView;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Ibexa\AdminUi\Specification\ContentType\ContentTypeIsUser;
use Ibexa\AdminUi\Specification\ContentType\ContentTypeIsUserGroup;
use Ibexa\AdminUi\Specification\OrSpecification;
use Ibexa\Contracts\AdminUi\Tab\ConditionalTabInterface;
use Ibexa\Contracts\AdminUi\Tab\AbstractEventDispatchingTab;
use Ibexa\Contracts\AdminUi\Tab\OrderedTabInterface;
use Ibexa\AdminUi\UI\Dataset\DatasetFactory;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use eZ\Publish\API\Repository\PermissionResolver;

class PoliciesTab extends AbstractEventDispatchingTab implements OrderedTabInterface, ConditionalTabInterface
{
    const URI_FRAGMENT = 'ibexa-tab-location-view-policies';

    /** @var \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory */
    protected $datasetFactory;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    protected $permissionResolver;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    protected $configResolver;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        DatasetFactory $datasetFactory,
        PermissionResolver $permissionResolver,
        EventDispatcherInterface $eventDispatcher,
        ConfigResolverInterface $configResolver
    ) {
        parent::__construct($twig, $translator, $eventDispatcher);

        $this->datasetFactory = $datasetFactory;
        $this->permissionResolver = $permissionResolver;
        $this->configResolver = $configResolver;
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

        $isUser = new ContentTypeIsUser($this->configResolver->getParameter('user_content_type_identifier'));
        $isUserGroup = new ContentTypeIsUserGroup($this->configResolver->getParameter('user_group_content_type_identifier'));
        $isUserOrUserGroup = (new OrSpecification($isUser, $isUserGroup))->isSatisfiedBy($contentType);

        return $isUserOrUserGroup;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate(): string
    {
        return '@ezdesign/content/tab/policies/tab.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateParameters(array $contextParameters = []): array
    {
        /** @var \eZ\Publish\API\Repository\Values\Content\Location $location */
        $location = $contextParameters['location'];

        $policiesPaginationParams = $contextParameters['policies_pagination_params'];

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

        return array_replace($contextParameters, $viewParameters);
    }
}

class_alias(PoliciesTab::class, 'EzSystems\EzPlatformAdminUi\Tab\LocationView\PoliciesTab');
