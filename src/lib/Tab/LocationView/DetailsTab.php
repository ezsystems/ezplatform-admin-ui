<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\LocationView;

use ArrayObject;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationAssignSubtreeData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationUpdateData;
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
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class DetailsTab extends AbstractEventDispatchingTab implements OrderedTabInterface
{
    const URI_FRAGMENT = 'ibexa-tab-location-view-details';

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
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
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
     * @inheritdoc
     */
    public function getTemplate(): string
    {
        return '@ezdesign/content/tab/details.html.twig';
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

        $versionInfo = $content->getVersionInfo();
        $contentInfo = $versionInfo->getContentInfo();

        $viewParameters = new ArrayObject([
            'content_info' => $contentInfo,
            'version_info' => $versionInfo,
        ]);

        $this->supplySectionParameters($viewParameters, $contentInfo, $location);
        $this->supplyObjectStateParameters($viewParameters, $contentInfo);
        $this->supplyTranslations($viewParameters, $versionInfo);
        $this->supplyFormLocationUpdate($viewParameters, $location);
        $this->supplyCreator($viewParameters, $contentInfo);
        $this->supplyLastContributor($viewParameters, $versionInfo);
        $this->supplySortFieldClauseMap($viewParameters);

        return array_replace($contextParameters, $viewParameters->getArrayCopy());
    }

    /**
     * @param \ArrayObject $parameters
     */
    private function supplySortFieldClauseMap(ArrayObject $parameters): void
    {
        $parameters['sort_field_clause_map'] = [
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
     * @param \ArrayObject $parameters
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo
     */
    private function supplyCreator(ArrayObject $parameters, ContentInfo $contentInfo): void
    {
        $parameters['creator'] = null;
        if ((new UserExists($this->userService))->isSatisfiedBy($contentInfo->ownerId)) {
            $parameters['creator'] = $this->userService->loadUser($contentInfo->ownerId);
        }
    }

    /**
     * @param \ArrayObject $parameters
     * @param \eZ\Publish\API\Repository\Values\Content\VersionInfo $versionInfo
     */
    private function supplyLastContributor(ArrayObject $parameters, VersionInfo $versionInfo): void
    {
        $parameters['last_contributor'] = null;
        if ((new UserExists($this->userService))->isSatisfiedBy($versionInfo->creatorId)) {
            $parameters['last_contributor'] = $this->userService->loadUser($versionInfo->creatorId);
        }
    }

    /**
     * @param \ArrayObject $parameters
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo
     */
    private function supplyObjectStateParameters(ArrayObject &$parameters, ContentInfo $contentInfo): void
    {
        $objectStatesDataset = $this->datasetFactory->objectStates();
        $objectStatesDataset->load($contentInfo);

        $canAssignObjectState = $this->canUserAssignObjectState();

        $parameters['object_states'] = $objectStatesDataset->getObjectStates();
        $parameters['can_assign'] = $canAssignObjectState;
        $parameters['form_state_update'] = [];

        if ($canAssignObjectState) {
            foreach ($objectStatesDataset->getObjectStates() as $objectState) {
                $objectStateGroup = $objectState->objectStateGroup;
                $objectStateUpdateForm = $this->formFactory->create(
                    ContentObjectStateUpdateType::class,
                    new ContentObjectStateUpdateData(
                        $contentInfo, $objectStateGroup, $objectState
                    )
                )->createView();

                $parameters['form_state_update'][$objectStateGroup->id] = $objectStateUpdateForm;
            }
        }
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
     * @param \ArrayObject $parameters
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     */
    private function supplySectionParameters(ArrayObject $parameters, ContentInfo $contentInfo, Location $location): void
    {
        $canSeeSection = $this->permissionResolver->canUser('section', 'view', $contentInfo);

        $parameters['section'] = null;
        $parameters['can_see_section'] = $canSeeSection;
        $parameters['form_assign_section'] = null;

        if ($canSeeSection) {
            $section = $this->sectionService->loadSection($contentInfo->sectionId);
            $parameters['section'] = $section;

            $canAssignSection = $this->permissionResolver->hasAccess('section', 'assign');
            if ($canAssignSection) {
                $assignSectionToSubtreeForm = $this->formFactory->create(
                    LocationAssignSectionType::class,
                    new LocationAssignSubtreeData(
                        $section, $location
                    )
                )->createView();

                $parameters['form_assign_section'] = $assignSectionToSubtreeForm;
            }
        }
    }

    /**
     * @param \ArrayObject $parameters
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     */
    private function supplyFormLocationUpdate(ArrayObject $parameters, Location $location): void
    {
        $parameters['form_location_update'] = $this->formFactory->create(
            LocationUpdateType::class,
            new LocationUpdateData($location)
        )->createView();
    }

    /**
     * @param \ArrayObject $parameters
     * @param \eZ\Publish\API\Repository\Values\Content\VersionInfo $versionInfo
     */
    private function supplyTranslations(ArrayObject $parameters, VersionInfo $versionInfo): void
    {
        $translationsDataset = $this->datasetFactory->translations();
        $translationsDataset->load($versionInfo);

        $parameters['translations'] = $translationsDataset->getTranslations();
    }
}
