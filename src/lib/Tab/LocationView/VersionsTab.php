<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\LocationView;

use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentEditData;
use EzSystems\EzPlatformAdminUi\Form\Data\Version\VersionRemoveData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Specification\ContentIsUser;
use EzSystems\EzPlatformAdminUi\Tab\AbstractEventDispatchingTab;
use EzSystems\EzPlatformAdminUi\Tab\ConditionalTabInterface;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class VersionsTab extends AbstractEventDispatchingTab implements OrderedTabInterface, ConditionalTabInterface
{
    public const FORM_REMOVE_DRAFT = 'version_remove_draft';
    public const FORM_REMOVE_ARCHIVED = 'version_remove_archived';
    const URI_FRAGMENT = 'ibexa-tab-location-view-versions';

    /** @var \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory */
    protected $datasetFactory;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    protected $formFactory;

    /** @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface */
    protected $urlGenerator;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    protected $permissionResolver;

    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /**
     * @param \Twig\Environment $twig
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     * @param \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory $datasetFactory
     * @param \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory $formFactory
     * @param \Symfony\Component\Routing\Generator\UrlGeneratorInterface $urlGenerator
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \eZ\Publish\API\Repository\UserService $userService
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        DatasetFactory $datasetFactory,
        FormFactory $formFactory,
        UrlGeneratorInterface $urlGenerator,
        PermissionResolver $permissionResolver,
        UserService $userService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($twig, $translator, $eventDispatcher);

        $this->datasetFactory = $datasetFactory;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
        $this->permissionResolver = $permissionResolver;
        $this->userService = $userService;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'versions';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        /** @Desc("Versions") */
        return $this->translator->trans('tab.name.versions', [], 'locationview');
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return 300;
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
        return $this->permissionResolver->canUser('content', 'versionread', $parameters['content']);
    }

    /**
     * @inheritdoc
     */
    public function getTemplate(): string
    {
        return '@ezdesign/content/tab/versions/tab.html.twig';
    }

    /**
     * @inheritdoc
     */
    public function getTemplateParameters(array $contextParameters = []): array
    {
        /** @var \eZ\Publish\API\Repository\Values\Content\Content $content */
        $content = $contextParameters['content'];
        /** @var \eZ\Publish\API\Repository\Values\Content\Location $location */
        $location = $contextParameters['location'];

        $draftPaginationParams = $contextParameters['draft_pagination_params'];

        $versionInfo = $content->getVersionInfo();
        $contentInfo = $versionInfo->getContentInfo();
        $versionsDataset = $this->datasetFactory->versions();
        $versionsDataset->load($contentInfo);

        $draftPagerfanta = new Pagerfanta(
            new ArrayAdapter($versionsDataset->getDraftVersions())
        );

        $draftPagerfanta->setMaxPerPage($draftPaginationParams['limit']);
        $draftPagerfanta->setCurrentPage(min($draftPaginationParams['page'], $draftPagerfanta->getNbPages()));

        /** @var \EzSystems\EzPlatformAdminUi\UI\Value\Content\VersionInfo[] $policies */
        $draftVersions = $draftPagerfanta->getCurrentPageResults();

        $archivedVersions = $versionsDataset->getArchivedVersions();

        $removeVersionDraftForm = $this->createVersionRemoveForm(
            $location,
            $draftVersions,
            true
        );
        $removeVersionArchivedForm = $this->createVersionRemoveForm(
            $location,
            $archivedVersions,
            false
        );
        $archivedVersionRestoreForm = $this->formFactory->contentEdit(
            new ContentEditData($contentInfo, null, null, $location),
            'archived_version_restore'
        );

        $parameters = [
            'versions_dataset' => $versionsDataset,
            'published_versions' => $versionsDataset->getPublishedVersions(),
            'archived_versions' => $archivedVersions,
            'form_version_remove_draft' => $removeVersionDraftForm->createView(),
            'form_version_remove_archived' => $removeVersionArchivedForm->createView(),
            'form_archived_version_restore' => $archivedVersionRestoreForm->createView(),
            'draft_pager' => $draftPagerfanta,
            'draft_pagination_params' => $draftPaginationParams,
            'content_is_user' => (new ContentIsUser($this->userService))->isSatisfiedBy($content),
        ];

        return array_replace($contextParameters, $parameters);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\VersionInfo[] $versions
     *
     * @return array
     */
    private function getVersionNumbers(array $versions): array
    {
        $versionNumbers = array_column($versions, 'versionNo');

        return array_combine($versionNumbers, array_fill_keys($versionNumbers, false));
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param array $versions
     * @param bool $isDraftForm
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createVersionRemoveForm(Location $location, array $versions, bool $isDraftForm): FormInterface
    {
        $contentInfo = $location->getContentInfo();
        $data = new VersionRemoveData($contentInfo, $this->getVersionNumbers($versions));

        $formName = sprintf('version-remove-%s', $isDraftForm
            ? self::FORM_REMOVE_DRAFT
            : self::FORM_REMOVE_ARCHIVED
        );

        return $this->formFactory->removeVersion($data, $formName);
    }
}
