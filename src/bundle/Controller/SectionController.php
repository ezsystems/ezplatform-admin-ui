<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\Values\Content\Section;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use eZ\Publish\Core\Repository\SearchService;
use EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionContentAssignData;
use EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionUpdateData;
use EzSystems\EzPlatformAdminUi\Form\DataMapper\SectionCreateMapper;
use EzSystems\EzPlatformAdminUi\Form\DataMapper\SectionUpdateMapper;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

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

    /**
     * @param NotificationHandlerInterface $notificationHandler
     * @param TranslatorInterface $translator
     * @param SectionService $sectionService
     * @param SearchService $searchService
     * @param FormFactory $formFactory
     * @param SectionCreateMapper $sectionCreateMapper
     * @param SectionUpdateMapper $sectionUpdateMapper
     * @param SubmitHandler $submitHandler
     */
    public function __construct(
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator,
        SectionService $sectionService,
        SearchService $searchService,
        FormFactory $formFactory,
        SectionCreateMapper $sectionCreateMapper,
        SectionUpdateMapper $sectionUpdateMapper,
        SubmitHandler $submitHandler
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
        $this->sectionService = $sectionService;
        $this->searchService = $searchService;
        $this->formFactory = $formFactory;
        $this->sectionCreateMapper = $sectionCreateMapper;
        $this->sectionUpdateMapper = $sectionUpdateMapper;
        $this->submitHandler = $submitHandler;
    }

    /**
     * @return Response
     */
    public function listAction(): Response
    {
        /** @var Section[] $sectionList */
        $sectionList = $this->sectionService->loadSections();
        $sectionListUrl = $this->generateUrl('ezplatform.section.list');

        $contentCountBySectionId = [];
        $deletableSections = [];
        $deleteFormsBySectionId = [];
        $assignContentFormsBySectionId = [];

        foreach ($sectionList as $section) {
            $contentCountBySectionId[$section->id] = $this->sectionService->countAssignedContents($section);
            $deletableSections[$section->id] = !$this->sectionService->isSectionUsed($section);

            $deleteFormsBySectionId[$section->id] = $this->formFactory->deleteSection(
                new SectionDeleteData($section),
                $sectionListUrl
            )->createView();

            $assignContentFormsBySectionId[$section->id] = $this->formFactory->assignContentSectionForm(
                new SectionContentAssignData($section),
                $sectionListUrl
            )->createView();
        }

        return $this->render('EzPlatformAdminUiBundle:admin/section:list.html.twig', [
            'can_edit' => $this->isGranted(new Attribute('section', 'edit')),
            'can_assign' => $this->isGranted(new Attribute('section', 'assign')),
            'sections' => $sectionList,
            'content_count' => $contentCountBySectionId,
            'deletable' => $deletableSections,
            'form_section_delete' => $deleteFormsBySectionId,
            'form_section_content_assign' => $assignContentFormsBySectionId,
        ]);
    }

    /**
     * @param Section $section
     *
     * @return Response
     */
    public function viewAction(Section $section): Response
    {
        $sectionListUrl = $this->generateUrl('ezplatform.section.list');
        $sectionViewUrl = $this->generateUrl('ezplatform.section.view', ['sectionId' => $section->id]);

        $sectionDeleteForm = $this->formFactory->deleteSection(
            new SectionDeleteData($section),
            $sectionListUrl
        )->createView();

        $sectionContentAssignForm = $this->formFactory->assignContentSectionForm(
            new SectionContentAssignData($section),
            $sectionViewUrl
        )->createView();

        return $this->render('EzPlatformAdminUiBundle:admin/section:view.html.twig', [
            'section' => $section,
            'form_section_delete' => $sectionDeleteForm,
            'form_section_content_assign' => $sectionContentAssignForm,
            'content_count' => $this->sectionService->countAssignedContents($section),
            'deletable' => !$this->sectionService->isSectionUsed($section),
            'can_edit' => $this->isGranted(new Attribute('section', 'edit')),
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
        $sectionListUrl = $this->generateUrl('ezplatform.section.list');

        $form = $this->formFactory->deleteSection(
            new SectionDeleteData($section),
            $sectionListUrl
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function(SectionDeleteData $data) {
                $section = $data->getSection();

                $this->sectionService->deleteSection($section);

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Section '%name%' removed.") */ 'section.delete.success',
                        ['%name%' => $section->name],
                        'section'
                    )
                );
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($sectionListUrl);
    }

    /**
     * @param Request $request
     * @param Section $section
     *
     * @return Response
     */
    public function assignContentAction(Request $request, Section $section): Response
    {
        $sectionViewUrl = $this->generateUrl('ezplatform.section.view', ['sectionId' => $section->id]);

        $form = $this->formFactory->assignContentSectionForm(
            new SectionContentAssignData($section),
            $sectionViewUrl
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function(SectionContentAssignData $data) {
                $section = $data->getSection();

                $contentInfos = array_column($data->getLocations(), 'contentInfo');

                foreach ($contentInfos as $contentInfo) {
                    $this->sectionService->assignSection($contentInfo, $section);
                }

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("%contentItemsCount% content items were assigned to '%name%'") */ 'section.assign_content.success',
                        ['%name%' => $section->name, '%contentItemsCount%' => count($contentInfos)],
                        'section'
                    )
                );
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        /* Fallback Redirect */
        return $this->redirect($sectionViewUrl);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request): Response
    {
        $sectionViewRoute = 'ezplatform.section.view';

        $form = $this->formFactory->createSection(
            new SectionCreateData(),
            $sectionViewRoute
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function(SectionCreateData $data) {
                $sectionCreateStruct = $this->sectionCreateMapper->reverseMap($data);
                $section = $this->sectionService->createSection($sectionCreateStruct);

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Section '%name%' created.") */ 'section.create.success',
                        ['%name%' => $section->name],
                        'section'
                    )
                );

                return [
                    'sectionId' => $section->id
                ];
            });

            if ($result instanceof Response) {
                return $result;
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
        $sectionViewUrl = $this->generateUrl('ezplatform.section.view', ['sectionId' => $section->id]);

        $form = $this->formFactory->updateSection(
            new SectionUpdateData($section),
            $sectionViewUrl
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function(SectionUpdateData $data) {
                $sectionUpdateStruct = $this->sectionUpdateMapper->reverseMap($data);
                $section = $this->sectionService->updateSection($data->getSection(), $sectionUpdateStruct);

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Section '%name%' updated.") */ 'section.update.success',
                        ['%name%' => $section->name],
                        'section'
                    )
                );
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('EzPlatformAdminUiBundle:admin/section:update.html.twig', [
            'section' => $section,
            'form_section_update' => $form->createView(),
        ]);
    }
}
