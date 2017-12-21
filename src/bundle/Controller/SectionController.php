<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\API\Repository\Values\Content\Section;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use eZ\Publish\Core\Pagination\Pagerfanta\ContentSearchAdapter;
use eZ\Publish\API\Repository\SearchService;
use EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionContentAssignData;
use EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionsDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionUpdateData;
use EzSystems\EzPlatformAdminUi\Form\DataMapper\SectionCreateMapper;
use EzSystems\EzPlatformAdminUi\Form\DataMapper\SectionUpdateMapper;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use EzSystems\EzPlatformAdminUi\UI\Service\PathService;
use EzSystems\EzPlatformAdminUiBundle\View\EzPagerfantaView;
use EzSystems\EzPlatformAdminUiBundle\View\Template\EzPagerfantaTemplate;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use Exception;

class SectionController extends Controller
{
    /** @var NotificationHandlerInterface */
    private $notificationHandler;

    /** @var TranslatorInterface */
    private $translator;

    /** @var SectionService */
    private $sectionService;

    /** @var SearchService */
    private $searchService;

    /** @var FormFactory */
    private $formFactory;

    /** @var SectionCreateMapper */
    private $sectionCreateMapper;

    /** @var SectionUpdateMapper */
    private $sectionUpdateMapper;

    /** @var SubmitHandler */
    private $submitHandler;

    /** @var ContentTypeService */
    private $contentTypeService;

    /** @var LocationService */
    private $locationService;

    /** @var PathService */
    private $pathService;

    /** @var int */
    private $defaultPaginationLimit;

    /**
     * @param NotificationHandlerInterface $notificationHandler
     * @param TranslatorInterface $translator
     * @param SectionService $sectionService
     * @param SearchService $searchService
     * @param FormFactory $formFactory
     * @param SectionCreateMapper $sectionCreateMapper
     * @param SectionUpdateMapper $sectionUpdateMapper
     * @param SubmitHandler $submitHandler
     * @param ContentTypeService $contentTypeService
     * @param LocationService $locationService
     * @param PathService $pathService
     * @param int $defaultPaginationLimit
     */
    public function __construct(
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator,
        SectionService $sectionService,
        SearchService $searchService,
        FormFactory $formFactory,
        SectionCreateMapper $sectionCreateMapper,
        SectionUpdateMapper $sectionUpdateMapper,
        SubmitHandler $submitHandler,
        ContentTypeService $contentTypeService,
        LocationService $locationService,
        PathService $pathService,
        int $defaultPaginationLimit
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
        $this->sectionService = $sectionService;
        $this->searchService = $searchService;
        $this->formFactory = $formFactory;
        $this->sectionCreateMapper = $sectionCreateMapper;
        $this->sectionUpdateMapper = $sectionUpdateMapper;
        $this->submitHandler = $submitHandler;
        $this->contentTypeService = $contentTypeService;
        $this->locationService = $locationService;
        $this->pathService = $pathService;
        $this->defaultPaginationLimit = $defaultPaginationLimit;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request): Response
    {
        $page = $request->query->get('page') ?? 1;

        $pagerfanta = new Pagerfanta(
            new ArrayAdapter($this->sectionService->loadSections())
        );

        $pagerfanta->setMaxPerPage($this->defaultPaginationLimit);
        $pagerfanta->setCurrentPage(min($page, $pagerfanta->getNbPages()));

        /** @var Section[] $sectionList */
        $sectionList = $pagerfanta->getCurrentPageResults();
        $contentCountBySectionId = [];
        $deletableSections = [];

        $deleteSectionsForm = $this->formFactory->deleteSections(
            new SectionsDeleteData($this->getSectionsNumbers($sectionList))
        );

        $assignContentForms = $this->formFactory->assignContentSectionForm(
            new SectionContentAssignData()
        );

        foreach ($sectionList as $section) {
            $contentCountBySectionId[$section->id] = $this->sectionService->countAssignedContents($section);
            $deletableSections[$section->id] = !$this->sectionService->isSectionUsed($section);
        }

        return $this->render('EzPlatformAdminUiBundle:admin/section:list.html.twig', [
            'can_edit' => $this->isGranted(new Attribute('section', 'edit')),
            'can_assign' => $this->isGranted(new Attribute('section', 'assign')),
            'pager' => $pagerfanta,
            'content_count' => $contentCountBySectionId,
            'deletable' => $deletableSections,
            'form_sections_delete' => $deleteSectionsForm->createView(),
            'form_section_content_assign' => $assignContentForms->createView(),
        ]);
    }

    /**
     * @param Section $section
     *
     * @return Response
     */
    public function viewAction(Section $section): Response
    {
        $sectionDeleteForm = $this->formFactory->deleteSection(
            new SectionDeleteData($section)
        )->createView();

        return $this->render('EzPlatformAdminUiBundle:admin/section:view.html.twig', [
            'section' => $section,
            'form_section_delete' => $sectionDeleteForm,
            'deletable' => !$this->sectionService->isSectionUsed($section),
            'can_edit' => $this->isGranted(new Attribute('section', 'edit')),
        ]);
    }

    /**
     * Fragment action which renders list of contents assigned to section.
     *
     * @param Section $section
     * @param int $page Current page
     * @param int $limit Number of items per page
     *
     * @return Response
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
                'type' => $this->contentTypeService->loadContentType($content->contentInfo->contentTypeId)->getName(),
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

        return $this->render('EzPlatformAdminUiBundle:admin/section:assigned_content.html.twig', [
            'section' => $section,
            'form_section_content_assign' => $sectionContentAssignForm,
            'assigned_content' => $assignedContent,
            'pagerfanta' => $pagerfanta,
            'pagination' => $pagination,
            'can_assign' => $this->isGranted(new Attribute('section', 'assign')),
        ]);
    }

    /**
     * @param Request $request
     * @param Section $section
     *
     * @return Response
     */
    public function deleteAction(Request $request, Section $section): Response
    {
        $form = $this->formFactory->deleteSection(
            new SectionDeleteData($section)
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (SectionDeleteData $data) {
                $section = $data->getSection();

                $this->sectionService->deleteSection($section);

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Section '%name%' removed.") */
                        'section.delete.success',
                        ['%name%' => $section->name],
                        'section'
                    )
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
     * @param Request $request
     *
     * @return Response
     *
     * @throws \InvalidArgumentException
     */
    public function bulkDeleteAction(Request $request): Response
    {
        $form = $this->formFactory->deleteSections(
            new SectionsDeleteData()
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->submitHandler->handle($form, function (SectionsDeleteData $data) {
                foreach ($data->getSections() as $sectionId => $selected) {
                    $section = $this->sectionService->loadSection($sectionId);
                    $this->sectionService->deleteSection($section);

                    $this->notificationHandler->success(
                        $this->translator->trans(
                            /** @Desc("Section '%name%' removed.") */
                            'section.delete.success',
                            ['%name%' => $section->name],
                            'section'
                        )
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
     * @param Request $request
     * @param Section $section
     *
     * @return Response
     */
    public function assignContentAction(Request $request, Section $section): Response
    {
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
                    $this->translator->trans(
                        /** @Desc("%contentItemsCount% content items were assigned to '%name%'") */
                        'section.assign_content.success',
                        ['%name%' => $section->name, '%contentItemsCount%' => count($contentInfos)],
                        'section'
                    )
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
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request): Response
    {
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
                    $this->translator->trans(
                        /** @Desc("Section '%name%' created.") */
                        'section.create.success',
                        ['%name%' => $section->name],
                        'section'
                    )
                );

                return new RedirectResponse($this->generateUrl('ezplatform.section.view', [
                    'sectionId' => $section->id,
                ]));
            } catch (Exception $e) {
                $this->notificationHandler->error($e->getMessage());
            }
        }

        return $this->render('EzPlatformAdminUiBundle:admin/section:create.html.twig', [
            'form_section_create' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Section $section
     *
     * @return Response
     */
    public function updateAction(Request $request, Section $section): Response
    {
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
                    $this->translator->trans(
                        /** @Desc("Section '%name%' updated.") */
                        'section.update.success',
                        ['%name%' => $section->name],
                        'section'
                    )
                );

                return new RedirectResponse($this->generateUrl('ezplatform.section.view', [
                    'sectionId' => $section->id,
                ]));
            } catch (Exception $e) {
                $this->notificationHandler->error($e->getMessage());
            }
        }

        return $this->render('EzPlatformAdminUiBundle:admin/section:update.html.twig', [
            'section' => $section,
            'form_section_update' => $form->createView(),
        ]);
    }

    /**
     * @param Section[] $sections
     *
     * @return array
     */
    private function getSectionsNumbers(array $sections): array
    {
        $sectionsNumbers = array_column($sections, 'id');

        return array_combine($sectionsNumbers, array_fill_keys($sectionsNumbers, false));
    }
}
