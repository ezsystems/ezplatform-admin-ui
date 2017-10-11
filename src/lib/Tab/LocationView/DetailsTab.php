<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\LocationView;

use eZ\Publish\API\Repository;
use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\Helper\FieldsGroups\FieldsGroupsList;
use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class DetailsTab extends AbstractTab implements OrderedTabInterface
{
    /** @var Repository\ContentTypeService */
    protected $contentTypeService;

    /** @var FieldsGroupsList */
    protected $fieldsGroupsListHelper;

    /** @var Repository\UserService */
    protected $userService;

    /** @var Repository\SectionService */
    protected $sectionService;

    /** @var DatasetFactory */
    protected $datasetFactory;

    /**
     * @param Environment $twig
     * @param TranslatorInterface $translator
     * @param SectionService $sectionService
     * @param UserService $userService
     * @param DatasetFactory $datasetFactory
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        SectionService $sectionService,
        UserService $userService,
        DatasetFactory $datasetFactory
    ) {
        parent::__construct($twig, $translator);

        $this->sectionService = $sectionService;
        $this->userService = $userService;
        $this->datasetFactory = $datasetFactory;
    }

    public function getIdentifier(): string
    {
        return 'details';
    }

    public function getName(): string
    {
        /** @Desc("Details") */
        return $this->translator->trans('tab.name.details', [], 'locationview');
    }

    public function getOrder(): int
    {
        return 200;
    }

    public function renderView(array $parameters): string
    {
        /** @var Content $content */
        $content = $parameters['content'];
        $versionInfo = $content->getVersionInfo();
        $contentInfo = $versionInfo->getContentInfo();
        $translationsDataset = $this->datasetFactory->translations();
        $translationsDataset->load($versionInfo);
        $viewParameters = [
            'section' => $this->sectionService->loadSection($contentInfo->sectionId),
            'contentInfo' => $contentInfo,
            'versionInfo' => $versionInfo,
            'creator' => $this->userService->loadUser($contentInfo->ownerId),
            'lastContributor' => $this->userService->loadUser($versionInfo->creatorId),
            'translations' => $translationsDataset->getTranslations(),
        ];

        return $this->twig->render(
            'EzPlatformAdminUiBundle:content/tab:details.html.twig',
            array_merge($viewParameters, $parameters)
        );
    }
}
