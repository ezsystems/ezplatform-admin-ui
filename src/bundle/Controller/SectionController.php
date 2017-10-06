<?php

namespace EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\Values\Content\Section;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use eZ\Publish\Core\Repository\SearchService;
use EzPlatformAdminUi\Form\Data\Section\SectionContentAssignData;
use EzPlatformAdminUi\Form\Data\Section\SectionCreateData;
use EzPlatformAdminUi\Form\Data\Section\SectionDeleteData;
use EzPlatformAdminUi\Form\Data\Section\SectionUpdateData;
use EzPlatformAdminUi\Form\Data\UiFormData;
use EzPlatformAdminUi\Form\DataMapper\SectionCreateMapper;
use EzPlatformAdminUi\Form\DataMapper\SectionUpdateMapper;
use EzPlatformAdminUi\Form\Factory\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class SectionController extends Controller
{
    /** @var SectionService */
    protected $sectionService;

    /** @var SearchService */
    private $searchService;

    /** @var TranslatorInterface */
    private $translator;

    /** @var FormFactory */
    private $formFactory;

    /** @var SectionCreateMapper */
    private $sectionCreateMapper;

    /** @var SectionUpdateMapper */
    private $sectionUpdateMapper;

    /**
     * @param SectionService $sectionService
     * @param SearchService $searchService
     * @param TranslatorInterface $translator
     * @param FormFactory $formFactory
     * @param SectionCreateMapper $sectionCreateMapper
     * @param SectionUpdateMapper $sectionUpdateMapper
     */
    public function __construct(
        SectionService $sectionService,
        SearchService $searchService,
        TranslatorInterface $translator,
        FormFactory $formFactory,
        SectionCreateMapper $sectionCreateMapper,
        SectionUpdateMapper $sectionUpdateMapper
    ) {
        $this->sectionService = $sectionService;
        $this->searchService = $searchService;
        $this->translator = $translator;
        $this->formFactory = $formFactory;
        $this->sectionCreateMapper = $sectionCreateMapper;
        $this->sectionUpdateMapper = $sectionUpdateMapper;
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
            $deleteFormsBySectionId[$section->id] = $this->getSectionDeleteForm(
                $section,
                $sectionListUrl
            )->createView();
            $assignContentFormsBySectionId[$section->id] = $this->getSectionContentAssignForm(
                $section,
                $sectionListUrl
            )->createView();
        }

        /** @todo: needs refactoring by introducing UI data classes */

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
     * @param int $sectionId
     *
     * @return Response
     */
    public function viewAction(int $sectionId): Response
    {
        /** @todo should be replaced with ParamConverter */
        $section = $this->sectionService->loadSection($sectionId);

        $sectionViewUrl = $this->generateUrl('ezplatform.section.view', ['sectionId' => $section->id]);
        $sectionDeleteForm = $this->getSectionDeleteForm($section, $this->generateUrl('ezplatform.section.list'));
        $sectionContentAssignForm = $this->getSectionContentAssignForm($section, $sectionViewUrl);

        return $this->render('EzPlatformAdminUiBundle:admin/section:view.html.twig', [
            'section' => $section,
            'form_section_delete' => $sectionDeleteForm->createView(),
            'form_section_content_assign' => $sectionContentAssignForm->createView(),
            'content_count' => $this->sectionService->countAssignedContents($section),
            'deletable' => !$this->sectionService->isSectionUsed($section),
            'can_edit' => $this->isGranted(new Attribute('section', 'edit')),
            'can_assign' => $this->isGranted(new Attribute('section', 'assign')),
        ]);
    }

    /**
     * @param Request $request
     * @param int $sectionId
     *
     * @return Response
     */
    public function deleteAction(Request $request, int $sectionId): Response
    {
        $form = $this->formFactory->deleteSection($sectionId);
        $form->handleRequest($request);

        /** @var UiFormData $uiFormData */
        $uiFormData = $form->getData();

        if ($form->isValid() && $form->isSubmitted()) {
            /** @var SectionDeleteData $sectionDeleteData */
            $sectionDeleteData = $uiFormData->getData();

            $section = $sectionDeleteData->getSection();
            $this->sectionService->deleteSection($section);

            $this->flashSuccess(/** @Desc("Section \"%sectionName%\" removed.") */
                'section.delete.success', [
                '%sectionName%' => $section->name,
            ], 'section');

            return $this->redirect($uiFormData->getOnSuccessRedirectionUrl());
        }

        /**
         * @todo We should implement a service for converting form errors into notifications
         */
        foreach ($form->getErrors(true, true) as $formError) {
            $this->addFlash('danger', $formError->getMessage());
        }

        return $this->redirect($uiFormData->getOnFailureRedirectionUrl());
    }

    /**
     * @param Request $request
     * @param int $sectionId
     *
     * @return Response
     */
    public function assignContentAction(Request $request, int $sectionId): Response
    {
        $form = $this->formFactory->assignContentSectionForm($sectionId);
        $form->handleRequest($request);

        /** @var UiFormData $uiFormData */
        $uiFormData = $form->getData();

        if ($form->isValid() && $form->isSubmitted()) {
            /** @var SectionContentAssignData $sectionContentAssignData */
            $sectionContentAssignData = $uiFormData->getData();

            $section = $sectionContentAssignData->getSection();
            $contentInfos = array_column($sectionContentAssignData->getLocations(), 'contentInfo');

            foreach ($contentInfos as $contentInfo) {
                $this->sectionService->assignSection($contentInfo, $section);
            }

            $this->flashSuccess(/** @Desc("%contentItemsCount% content items were assigned to \"%sectionName%\"") */
                'section.assign_content.success', [
                '%sectionName%' => $section->name,
                '%contentItemsCount%' => count($contentInfos),
            ], 'section');


            return $this->redirect($uiFormData->getOnSuccessRedirectionUrl());
        }

        /**
         * @todo We should implement a service for converting form errors into notifications
         */
        foreach ($form->getErrors(true, true) as $formError) {
            $this->addFlash('danger', $formError->getMessage());
        }

        return $this->redirect($uiFormData->getOnFailureRedirectionUrl());
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request): Response
    {
        $form = $this->formFactory->createSection();
        $form->handleRequest($request);

        /** @var UiFormData $uiFormData */
        $uiFormData = $form->getData();

        if ($form->isValid() && $form->isSubmitted()) {
            /** @var SectionCreateData $sectionCreateData */
            $sectionCreateData = $uiFormData->getData();

            $sectionCreateStruct = $this->sectionCreateMapper->reverseMap($sectionCreateData);
            $section = $this->sectionService->createSection($sectionCreateStruct);

            $this->flashSuccess('section.create.success', [
                '%sectionName%' => $section->name,
            ], 'section');

            return $this->redirectToRoute('ezplatform.section.view', ['sectionId' => $section->id]);
        }

        return $this->render('EzPlatformAdminUiBundle:admin/section:create.html.twig', [
            'form_section_create' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param int|null $sectionId
     *
     * @return Response
     */
    public function updateAction(Request $request, ?int $sectionId): Response
    {
        /** @todo Add ParamConverter */
        $section = $this->sectionService->loadSection($sectionId);

        $form = $this->formFactory->updateSection(
            $section->id,
            new SectionUpdateData($section->identifier, $section->name)
        );
        $form->handleRequest($request);

        /** @var UiFormData $uiFormData */
        $uiFormData = $form->getData();

        if ($form->isValid() && $form->isSubmitted()) {
            /** @var SectionUpdateData $sectionUpdateData */
            $sectionUpdateData = $uiFormData->getData();

            $sectionUpdateStruct = $this->sectionUpdateMapper->reverseMap($sectionUpdateData);
            $section = $this->sectionService->updateSection($section, $sectionUpdateStruct);

            $this->flashSuccess('section.update.success', [
                '%sectionName%' => $section->name,
            ], 'section');

            return $this->redirectToRoute('ezplatform.section.view', ['sectionId' => $section->id]);
        }

        return $this->render('EzPlatformAdminUiBundle:admin/section:update.html.twig', [
            'section' => $section,
            'form_section_update' => $form->createView(),
        ]);
    }

    /**
     * @param Section $section
     * @param string $redirectionUrl
     *
     * @return FormInterface
     */
    private function getSectionDeleteForm(Section $section, string $redirectionUrl): FormInterface
    {
        return $this->formFactory->deleteSection(
            $section->id,
            new SectionDeleteData($section),
            $redirectionUrl,
            $redirectionUrl
        );
    }

    /**
     * @param Section $section
     * @param string $redirectionUrl
     *
     * @return FormInterface
     */
    private function getSectionContentAssignForm(Section $section, string $redirectionUrl): FormInterface
    {
        return $this->formFactory->assignContentSectionForm(
            $section->id,
            new SectionContentAssignData($section, []),
            $redirectionUrl,
            $redirectionUrl
        );
    }
}
