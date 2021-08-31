<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use Exception;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\API\Repository\Values\Content\Section;
use eZ\Publish\API\Repository\Values\User\Limitation\NewSectionLimitation;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use eZ\Publish\Core\Pagination\Pagerfanta\ContentSearchAdapter;
use EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionContentAssignData;
use EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionsDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionUpdateData;
use EzSystems\EzPlatformAdminUi\Form\DataMapper\SectionCreateMapper;
use EzSystems\EzPlatformAdminUi\Form\DataMapper\SectionUpdateMapper;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface;
use EzSystems\EzPlatformAdminUi\Permission\PermissionCheckerInterface;
use EzSystems\EzPlatformAdminUi\UI\Service\PathService;
use EzSystems\EzPlatformAdminUiBundle\View\EzPagerfantaView;
use EzSystems\EzPlatformAdminUiBundle\View\Template\EzPagerfantaTemplate;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class SectionController extends Controller
{
    /** @var \EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    /** @var \eZ\Publish\API\Repository\SectionService */
    private $sectionService;

    /** @var \eZ\Publish\API\Repository\SearchService */
    private $searchService;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    private $formFactory;

    /** @var \EzSystems\EzPlatformAdminUi\Form\DataMapper\SectionCreateMapper */
    private $sectionCreateMapper;

    /** @var \EzSystems\EzPlatformAdminUi\Form\DataMapper\SectionUpdateMapper */
    private $sectionUpdateMapper;

    /** @var \EzSystems\EzPlatformAdminUi\Form\SubmitHandler */
    private $submitHandler;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Service\PathService */
    private $pathService;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \EzSystems\EzPlatformAdminUi\Permission\PermissionCheckerInterface */
    private $permissionChecker;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    public function __construct(
        TranslatableNotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator,
        SectionService $sectionService,
        SearchService $searchService,
        FormFactory $formFactory,
        SectionCreateMapper $sectionCreateMapper,
        SectionUpdateMapper $sectionUpdateMapper,
        SubmitHandler $submitHandler,
        LocationService $locationService,
        PathService $pathService,
        PermissionResolver $permissionResolver,
        PermissionCheckerInterface $permissionChecker,
        ConfigResolverInterface $configResolver
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
        $this->sectionService = $sectionService;
        $this->searchService = $searchService;
        $this->formFactory = $formFactory;
        $this->sectionCreateMapper = $sectionCreateMapper;
        $this->sectionUpdateMapper = $sectionUpdateMapper;
        $this->submitHandler = $submitHandler;
        $this->locationService = $locationService;
        $this->pathService = $pathService;
        $this->permissionResolver = $permissionResolver;
        $this->permissionChecker = $permissionChecker;
        $this->configResolver = $configResolver;
    }

    public function performAccessCheck(): void
    {
        parent::performAccessCheck();
        $this->denyAccessUnlessGranted(new Attribute('section', 'view'));
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function listAction(Request $request): Response
    {
        $page = $request->query->get('page') ?? 1;

        $pagerfanta = new Pagerfanta(
            new ArrayAdapter($this->sectionService->loadSections())
        );

        $pagerfanta->setMaxPerPage($this->configResolver->getParameter('pagination.section_limit'));
        $pagerfanta->setCurrentPage(min($page, $pagerfanta->getNbPages()));

        /** @var \eZ\Publish\API\Repository\Values\Content\Section[] $sectionList */
        $sectionList = $pagerfanta->getCurrentPageResults();
        $contentCountBySectionId = [];
        $deletableSections = [];
        $assignableSections = [];

        $deleteSectionsForm = $this->formFactory->deleteSections(
            new SectionsDeleteData($this->getSectionsNumbers($sectionList))
        );

        $assignContentForms = $this->formFactory->assignContentSectionForm(
            new SectionContentAssignData()
        );

        foreach ($sectionList as $section) {
            $contentCountBySectionId[$section->id] = $this->sectionService->countAssignedContents($section);
            $deletableSections[$section->id] = !$this->sectionService->isSectionUsed($section);
            $assignableSections[$section->id] = $this->canUserAssignSectionToSomeContent($section);
        }

        $canEdit = $this->permissionResolver->hasAccess('section', 'edit');
        $canAssign = $this->permissionResolver->hasAccess('section', 'assign');

        // User can add Section only if he has access to edit and view.
        // View Policy must be without any limitation because the user must see newly created Section.
        $canAdd = $this->permissionResolver->hasAccess('section', 'view') === true
            && $this->permissionResolver->hasAccess('section', 'edit') === true;

        return $this->render('@ezdesign/section/list.html.twig', [
            'can_add' => $canAdd,
            'can_edit' => $canEdit,
            'can_assign' => $canAssign,
            'pager' => $pagerfanta,
            'content_count' => $contentCountBySectionId,
            'deletable' => $deletableSections,
            'assignable' => $assignableSections,
            'form_sections_delete' => $deleteSectionsForm->createView(),
            'form_section_content_assign' => $assignContentForms->createView(),
        ]);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Section $section
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Section $section): Response
    {
        $sectionDeleteForm = $this->formFactory->deleteSection(
            new SectionDeleteData($section)
        )->createView();

        return $this->render('@ezdesign/section/view.html.twig', [
            'section' => $section,
            'form_section_delete' => $sectionDeleteForm,
            'deletable' => !$this->sectionService->isSectionUsed($section),
            'can_edit' => $this->isGranted(new Attribute('section', 'edit')),
        ]);
    }

    /**
     * Fragment action which renders list of contents assigned to section.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Section $section
     * @param int $page Current page
     * @param int $limit Number of items per page
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function viewSectionContentAction(Section $section, int $page = 1, int $limit = 10): Response
    {
        $sectionContentAssignForm = $this->formFactory->assignContentSectionForm(
            new SectionContentAssignData($section)
        )->createView();

        $query = new Query();
        $query->sortClauses[] = new SortClause\ContentName(Query::SORT_ASC);
        $query->filter = new Query\Criterion\SectionId([
            $section->id,
        ]);

        $pagerfanta = new Pagerfanta(new ContentSearchAdapter($query, $this->searchService));
        $pagerfanta->setMaxPerPage($limit);
        $pagerfanta->setCurrentPage($page);

        $assignedContent = [];
        foreach ($pagerfanta as $content) {
            $assignedContent[] = [
                'id' => $content->id,
                'name' => $content->getName(),
                'type' => $content->getContentType()->getName(),
                'path' => $this->pathService->loadPathLocations(
                    $this->locationService->loadLocation($content->contentInfo->mainLocationId)
                ),
            ];
        }

        $routeGenerator = function ($page) use ($section) {
            return $this->generateUrl('ezplatform.section.view', [
                'sectionId' => $section->id,
                'page' => $page,
            ]);
        };

        $pagination = (new EzPagerfantaView(new EzPagerfantaTemplate($this->translator)))->render($pagerfanta, $routeGenerator);

        return $this->render('@ezdesign/section/assigned_content.html.twig', [
            'section' => $section,
            'form_section_content_assign' => $sectionContentAssignForm,
            'assigned_content' => $assignedContent,
            'pagerfanta' => $pagerfanta,
            'pagination' => $pagination,
            'can_assign' => $this->canUserAssignSectionToSomeContent($section),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \eZ\Publish\API\Repository\Values\Content\Section $section
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, Section $section): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('section', 'edit'));
        $form = $this->formFactory->deleteSection(
            new SectionDeleteData($section)
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (SectionDeleteData $data) {
                $section = $data->getSection();

                $this->sectionService->deleteSection($section);

                $this->notificationHandler->success(
                    /** @Desc("Section '%name%' removed.") */
                    'section.delete.success',
                    ['%name%' => $section->name],
                    'section'
                );

                return new RedirectResponse($this->generateUrl('ezplatform.section.list'));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('ezplatform.section.list'));
    }

    /**
     * Handles removing sections based on submitted form.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function bulkDeleteAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('section', 'edit'));
        $form = $this->formFactory->deleteSections(
            new SectionsDeleteData()
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (SectionsDeleteData $data) {
                foreach ($data->getSections() as $sectionId => $selected) {
                    $section = $this->sectionService->loadSection($sectionId);
                    $this->sectionService->deleteSection($section);

                    $this->notificationHandler->success(
                        /** @Desc("Section '%name%' removed.") */
                        'section.delete.success',
                        ['%name%' => $section->name],
                        'section'
                    );
                }
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('ezplatform.section.list'));
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \eZ\Publish\API\Repository\Values\Content\Section $section
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function assignContentAction(Request $request, Section $section): Response
    {
        if (!$this->canUserAssignSectionToSomeContent($section)) {
            $exception = $this->createAccessDeniedException();
            $exception->setAttributes('state');
            $exception->setSubject('assign');

            throw $exception;
        }

        $form = $this->formFactory->assignContentSectionForm(
            new SectionContentAssignData()
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (SectionContentAssignData $data) {
                $section = $data->getSection();

                $contentInfos = array_column($data->getLocations(), 'contentInfo');

                foreach ($contentInfos as $contentInfo) {
                    $this->sectionService->assignSection($contentInfo, $section);
                }

                $this->notificationHandler->success(
                    /** @Desc("%contentItemsCount% Content items assigned to '%name%'") */
                    'section.assign_content.success',
                    ['%name%' => $section->name, '%contentItemsCount%' => \count($contentInfos)],
                    'section'
                );

                return new RedirectResponse($this->generateUrl('ezplatform.section.view', [
                    'sectionId' => $section->id,
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('ezplatform.section.view', [
            'sectionId' => $section->id,
        ]));
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('section', 'edit'));
        $form = $this->formFactory->createSection(
            new SectionCreateData()
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            try {
                $sectionCreateStruct = $this->sectionCreateMapper->reverseMap($data);
                $section = $this->sectionService->createSection($sectionCreateStruct);

                $this->notificationHandler->success(
                    /** @Desc("Section '%name%' created.") */
                    'section.create.success',
                    ['%name%' => $section->name],
                    'section'
                );

                return new RedirectResponse($this->generateUrl('ezplatform.section.view', [
                    'sectionId' => $section->id,
                ]));
            } catch (Exception $e) {
                $this->notificationHandler->error(/** @Ignore */ $e->getMessage());
            }
        }

        return $this->render('@ezdesign/section/create.html.twig', [
            'form_section_create' => $form->createView(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \eZ\Publish\API\Repository\Values\Content\Section $section
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, Section $section): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('section', 'edit'));
        $form = $this->formFactory->updateSection(
            new SectionUpdateData($section)
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            try {
                $sectionUpdateStruct = $this->sectionUpdateMapper->reverseMap($data);
                $section = $this->sectionService->updateSection($data->getSection(), $sectionUpdateStruct);

                $this->notificationHandler->success(
                    /** @Desc("Section '%name%' updated.") */
                    'section.update.success',
                    ['%name%' => $section->name],
                    'section'
                );

                return new RedirectResponse($this->generateUrl('ezplatform.section.view', [
                    'sectionId' => $section->id,
                ]));
            } catch (Exception $e) {
                $this->notificationHandler->error(/** @Ignore */ $e->getMessage());
            }
        }

        return $this->render('@ezdesign/section/update.html.twig', [
            'section' => $section,
            'form_section_update' => $form->createView(),
        ]);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Section[] $sections
     *
     * @return array
     */
    private function getSectionsNumbers(array $sections): array
    {
        $sectionsNumbers = array_column($sections, 'id');

        return array_combine($sectionsNumbers, array_fill_keys($sectionsNumbers, false));
    }

    /**
     * Specifies if the User has access to assigning a given Section to Content.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Section $section
     *
     * @return bool
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    private function canUserAssignSectionToSomeContent(Section $section): bool
    {
        $hasAccess = $this->permissionResolver->hasAccess('section', 'assign');

        if (\is_bool($hasAccess)) {
            return $hasAccess;
        }

        $restrictedNewSections = $this->permissionChecker->getRestrictions($hasAccess, NewSectionLimitation::class);
        if (!empty($restrictedNewSections)) {
            return \in_array($section->id, array_map('intval', $restrictedNewSections), true);
        }

        // If a user has other limitation than NewSectionLimitation, then a decision will be taken later, based on selected Content.
        return true;
    }
}
