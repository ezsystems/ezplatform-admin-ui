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
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ContentObjectStateUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Specification\UserExists;
use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Location;

class DetailsTab extends AbstractTab implements OrderedTabInterface
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

    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    private $formFactory;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /**
     * @param \Twig\Environment $twig
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \eZ\Publish\API\Repository\SectionService $sectionService
     * @param \eZ\Publish\API\Repository\UserService $userService
     * @param \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory $datasetFactory
     * @param \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory $formFactory
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        SectionService $sectionService,
        UserService $userService,
        DatasetFactory $datasetFactory,
        FormFactory $formFactory,
        PermissionResolver $permissionResolver
    ) {
        parent::__construct($twig, $translator);

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
     * @param array $parameters
     *
     * @return string
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function renderView(array $parameters): string
    {
        /** @var \eZ\Publish\API\Repository\Values\Content\Content $content */
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
        foreach ($objectStatesDataset->getObjectStates() as $objectState) {
            $contentObjectStateUpdateTypeByGroupId[$objectState->objectStateGroup->id] = $this->formFactory->updateContentObjectState(
                new ContentObjectStateUpdateData($contentInfo, $objectState->objectStateGroup, $objectState)
            )->createView();
        }

        $creator = (new UserExists($this->userService))->isSatisfiedBy($contentInfo->ownerId)
            ? $this->userService->loadUser($contentInfo->ownerId) : null;

        $lastContributor = (new UserExists($this->userService))->isSatisfiedBy($versionInfo->creatorId)
            ? $this->userService->loadUser($versionInfo->creatorId) : null;

        $canSeeSection = $this->permissionResolver->hasAccess('section', 'view');

        $viewParameters = [
            'section' => $canSeeSection ? $this->sectionService->loadSection($contentInfo->sectionId) : null,
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
}
