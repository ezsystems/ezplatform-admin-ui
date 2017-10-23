<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\LocationView;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use EzSystems\EzPlatformAdminUi\Form\Data\Version\VersionRemoveData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class VersionsTab extends AbstractTab implements OrderedTabInterface
{
    public const FORM_REMOVE_DRAFT = 'version_remove_draft';
    public const FORM_REMOVE_ARCHIVED = 'version_remove_archived';

    /** @var DatasetFactory */
    protected $datasetFactory;

    /** @var FormFactory */
    protected $formFactory;

    /** @var UrlGeneratorInterface */
    protected $urlGenerator;

    /**
     * @param Environment $twig
     * @param TranslatorInterface $translator
     * @param DatasetFactory $datasetFactory
     * @param FormFactory $formFactory
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        DatasetFactory $datasetFactory,
        FormFactory $formFactory,
        UrlGeneratorInterface $urlGenerator
    ) {
        parent::__construct($twig, $translator);

        $this->datasetFactory = $datasetFactory;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
    }

    public function getIdentifier(): string
    {
        return 'versions';
    }

    public function getName(): string
    {
        /** @Desc("Versions") */
        return $this->translator->trans('tab.name.versions', [], 'locationview');
    }

    public function getOrder(): int
    {
        return 300;
    }

    public function renderView(array $parameters): string
    {
        /** @var Content $content */
        $content = $parameters['content'];
        /** @var Location $location */
        $location = $parameters['location'];
        $versionInfo = $content->getVersionInfo();
        $contentInfo = $versionInfo->getContentInfo();
        $versionsDataset = $this->datasetFactory->versions();
        $versionsDataset->load($contentInfo);

        $draftVersions = $versionsDataset->getDraftVersions();
        $archivedVersions = $versionsDataset->getArchivedVersions();

        $removeVersionDraftForm = $this->createVersionRemoveForm(
            $location,
            $draftVersions,
            self::FORM_REMOVE_DRAFT
        );
        $removeVersionArchivedForm = $this->createVersionRemoveForm(
            $location,
            $archivedVersions,
            self::FORM_REMOVE_ARCHIVED
        );

        $viewParameters = [
            'published_versions' => $versionsDataset->getPublishedVersions(),
            'draft_versions' => $draftVersions,
            'archived_versions' => $archivedVersions,
            'form_version_remove_draft' => $removeVersionDraftForm->createView(),
            'form_version_remove_archived' => $removeVersionArchivedForm->createView(),
        ];

        return $this->twig->render(
            'EzPlatformAdminUiBundle:content/tab/versions:tab.html.twig',
            array_merge($viewParameters, $parameters)
        );
    }

    /**
     * @param VersionInfo[] $versions
     *
     * @return array
     */
    private function getVersionNumbers(array $versions): array
    {
        $versionNumbers = array_column($versions, 'versionNo');

        return array_combine($versionNumbers, array_fill_keys($versionNumbers, false));
    }

    /**
     * @param Location $location
     * @param VersionInfo[] $versions
     * @param string $name
     *
     * @return FormInterface
     */
    private function createVersionRemoveForm(Location $location, array $versions, string $name): FormInterface
    {
        $contentInfo = $location->getContentInfo();
        $data = new VersionRemoveData($contentInfo, $this->getVersionNumbers($versions));

        $locationViewUrl = $this->urlGenerator->generate($location, ['_fragment' => 'ez-tab-location-view-versions']);

        return $this->formFactory->removeVersion($data, $locationViewUrl, $locationViewUrl);
    }
}
