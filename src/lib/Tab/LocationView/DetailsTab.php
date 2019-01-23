<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\LocationView;

use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\Section;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectState;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationAssignSubtreeData;
use EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ContentObjectStateUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Type\Location\LocationAssignSectionType;
use EzSystems\EzPlatformAdminUi\Form\Type\Location\LocationUpdateType;
use EzSystems\EzPlatformAdminUi\Form\Type\ObjectState\ContentObjectStateUpdateType;
use EzSystems\EzPlatformAdminUi\Specification\UserExists;
use EzSystems\EzPlatformAdminUi\Tab\AbstractEventDispatchingTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class DetailsTab extends AbstractEventDispatchingTab implements OrderedTabInterface
{
    const URI_FRAGMENT = 'ez-tab-location-view-details';

    /** @var \eZ\Publish\Core\Helper\FieldsGroups\FieldsGroupsList */
    protected $fieldsGroupsListHelper;

    /** @var \eZ\Publish\API\Repository\UserService */
    protected $userService;

    /** @var \eZ\Publish\API\Repository\SectionService */
    protected $sectionService;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory */
    protected $datasetFactory;

    /** @var \Symfony\Component\Form\FormFactoryInterface */
    private $formFactory;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /**
     * @param \Twig\Environment $twig
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \eZ\Publish\API\Repository\SectionService $sectionService
     * @param \eZ\Publish\API\Repository\UserService $userService
     * @param \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory $datasetFactory
     * @param \Symfony\Component\Form\FormFactoryInterface $formFactory
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        SectionService $sectionService,
        UserService $userService,
        DatasetFactory $datasetFactory,
        FormFactoryInterface $formFactory,
        PermissionResolver $permissionResolver,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($twig, $translator, $eventDispatcher);

        $this->sectionService = $sectionService;
        $this->userService = $userService;
        $this->datasetFactory = $datasetFactory;
        $this->formFactory = $formFactory;
        $this->permissionResolver = $permissionResolver;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'details';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        /** @Desc("Details") */
        return $this->translator->trans('tab.name.details', [], 'locationview');
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return 200;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate(): string
    {
        return '@ezdesign/content/tab/details.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateParameters(array $contextParameters = []): array
    {
        /** @var \eZ\Publish\API\Repository\Values\Content\Content $content */
        $content = $contextParameters['content'];
        $versionInfo = $content->getVersionInfo();
        $contentInfo = $versionInfo->getContentInfo();
        $translationsDataset = $this->datasetFactory->translations();
        $translationsDataset->load($versionInfo);
        $locationUpdateType = $this->createUpdateLocationForm($contextParameters['location']);
        $objectStatesDataset = $this->datasetFactory->objectStates();
        $objectStatesDataset->load($contentInfo);

        $contentObjectStateUpdateTypeByGroupId = [];
        foreach ($objectStatesDataset->getObjectStates() as $objectState) {
            $contentObjectStateUpdateTypeByGroupId[$objectState->objectStateGroup->id] = $this
                ->createUpdateContentObjectStateForm($contentInfo, $objectState)
                ->createView();
        }

        $creator = (new UserExists($this->userService))->isSatisfiedBy($contentInfo->ownerId)
            ? $this->userService->loadUser($contentInfo->ownerId) : null;

        $lastContributor = (new UserExists($this->userService))->isSatisfiedBy($versionInfo->creatorId)
            ? $this->userService->loadUser($versionInfo->creatorId) : null;

        $section = null;
        $canSeeSection = $this->permissionResolver->hasAccess('section', 'view');
        if ($canSeeSection) {
            $section = $this->sectionService->loadSection($contentInfo->sectionId);
        }

        $canAssignSection = $this->permissionResolver->hasAccess('section', 'assign');
        $assignSectionForm = null;
        if ($canSeeSection && $canAssignSection) {
            $assignSectionForm = $this->createLocationAssignSectionTypeForm(
                $contextParameters['location'], $section
            )->createView();
        }

        $viewParameters = [
            'section' => $section,
            'contentInfo' => $contentInfo,
            'versionInfo' => $versionInfo,
            'creator' => $creator,
            'lastContributor' => $lastContributor,
            'translations' => $translationsDataset->getTranslations(),
            'form_location_update' => $locationUpdateType->createView(),
            'objectStates' => $objectStatesDataset->getObjectStates(),
            'sort_field_clause_map' => $this->getSortFieldClauseMap(),
            'form_state_update' => $contentObjectStateUpdateTypeByGroupId,
            'can_see_section' => $canSeeSection,
            'can_assign' => $this->canUserAssignObjectState(),
            'form_assign_section' => $assignSectionForm,
        ];

        return array_replace($contextParameters, $viewParameters);
    }

    /**
     * @return array
     */
    private function getSortFieldClauseMap(): array
    {
        return [
            Location::SORT_FIELD_PATH => 'LocationPath',
            Location::SORT_FIELD_PUBLISHED => 'DatePublished',
            Location::SORT_FIELD_MODIFIED => 'DateModified',
            Location::SORT_FIELD_SECTION => 'SectionIdentifier',
            Location::SORT_FIELD_DEPTH => 'LocationDepth',
            Location::SORT_FIELD_PRIORITY => 'LocationPriority',
            Location::SORT_FIELD_NAME => 'ContentName',
            Location::SORT_FIELD_NODE_ID => 'LocationId',
            Location::SORT_FIELD_CONTENTOBJECT_ID => 'ContentId',
        ];
    }

    /**
     * Specifies if the User has access to assigning a given Object State to Content Info.
     *
     * @return bool
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    private function canUserAssignObjectState(): bool
    {
        return $this->permissionResolver->hasAccess('state', 'assign') !== false;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo
     * @param \eZ\Publish\API\Repository\Values\ObjectState\ObjectState $objectState
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createUpdateContentObjectStateForm(ContentInfo $contentInfo, ObjectState $objectState): FormInterface
    {
        return $this->formFactory->create(
            ContentObjectStateUpdateType::class,
            new ContentObjectStateUpdateData(
                $contentInfo, $objectState->objectStateGroup, $objectState
            )
        );
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createUpdateLocationForm(Location $location): FormInterface
    {
        return $this->formFactory->create(
            LocationUpdateType::class,
            new LocationUpdateData($location)
        );
    }

    /**
     * Creates assign section to subtree form.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param \eZ\Publish\API\Repository\Values\Content\Section $section
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createLocationAssignSectionTypeForm(Location $location, Section $section): FormInterface
    {
        return $this->formFactory->create(
            LocationAssignSectionType::class,
            new LocationAssignSubtreeData(
                $section, $location
            )
        );
    }
}
