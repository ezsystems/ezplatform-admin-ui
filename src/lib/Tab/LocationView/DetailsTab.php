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
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ContentObjectStateUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Specification\UserExists;
use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class DetailsTab extends AbstractTab implements OrderedTabInterface
{
    const URI_FRAGMENT = 'ez-tab-location-view-details';

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

    /** @var FormFactory */
    private $formFactory;

    /**
     * @param Environment $twig
     * @param TranslatorInterface $translator
     * @param SectionService $sectionService
     * @param UserService $userService
     * @param DatasetFactory $datasetFactory
     * @param FormFactory $formFactory
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        SectionService $sectionService,
        UserService $userService,
        DatasetFactory $datasetFactory,
        FormFactory $formFactory
    ) {
        parent::__construct($twig, $translator);

        $this->sectionService = $sectionService;
        $this->userService = $userService;
        $this->datasetFactory = $datasetFactory;
        $this->formFactory = $formFactory;
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
        $locationUpdateType = $this->formFactory->updateLocation(
            new LocationUpdateData($parameters['location'])
        );
        $objectStatesDataset = $this->datasetFactory->objectStates();
        $objectStatesDataset->load($contentInfo);
        $contentObjectStateUpdateTypeByGroupId = [];
        $contentObjectStateUpdateCanAssignGroupId = [];
        foreach ($objectStatesDataset->getObjectStates() as $objectState) {
            $contentObjectStateUpdateTypeByGroupId[$objectState->objectStateGroup->id] = $this->formFactory->updateContentObjectState(
                new ContentObjectStateUpdateData($contentInfo, $objectState->objectStateGroup, $objectState)
            )->createView();
            $contentObjectStateUpdateCanAssignGroupId[$objectState->objectStateGroup->id] = $objectState->userCanAssign;
        }

        $creator = (new UserExists($this->userService))->isSatisfiedBy($contentInfo->ownerId)
            ? $this->userService->loadUser($contentInfo->ownerId) : null;

        $lastContributor = (new UserExists($this->userService))->isSatisfiedBy($versionInfo->creatorId)
            ? $this->userService->loadUser($versionInfo->creatorId) : null;

        $viewParameters = [
            'section' => $this->sectionService->loadSection($contentInfo->sectionId),
            'contentInfo' => $contentInfo,
            'versionInfo' => $versionInfo,
            'creator' => $creator,
            'lastContributor' => $lastContributor,
            'translations' => $translationsDataset->getTranslations(),
            'form_location_update' => $locationUpdateType->createView(),
            'objectStates' => $objectStatesDataset->getObjectStates(),
            'sort_field_clause_map' => $this->getSortFieldClauseMap(),
            'form_state_update' => $contentObjectStateUpdateTypeByGroupId,
            'state_update_disabled' => $contentObjectStateUpdateCanAssignGroupId,
        ];

        return $this->twig->render(
            'EzPlatformAdminUiBundle:content/tab:details.html.twig',
            array_merge($viewParameters, $parameters)
        );
    }

    /**
     * @return array
     */
    private function getSortFieldClauseMap(): array
    {
        return [
            Repository\Values\Content\Location::SORT_FIELD_PATH => 'LocationPath',
            Repository\Values\Content\Location::SORT_FIELD_PUBLISHED => 'DatePublished',
            Repository\Values\Content\Location::SORT_FIELD_MODIFIED => 'DateModified',
            Repository\Values\Content\Location::SORT_FIELD_SECTION => 'SectionIdentifier',
            Repository\Values\Content\Location::SORT_FIELD_DEPTH => 'LocationDepth',
            Repository\Values\Content\Location::SORT_FIELD_PRIORITY => 'LocationPriority',
            Repository\Values\Content\Location::SORT_FIELD_NAME => 'ContentName',
            Repository\Values\Content\Location::SORT_FIELD_NODE_ID => 'LocationId',
            Repository\Values\Content\Location::SORT_FIELD_CONTENTOBJECT_ID => 'ContentId',
        ];
    }
}
