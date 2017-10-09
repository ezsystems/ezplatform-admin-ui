<?php

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\ContentService;
use EzSystems\EzPlatformAdminUi\Form\Data\UiFormData;
use EzSystems\EzPlatformAdminUi\Form\Data\Version\VersionRemoveData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Tab\LocationView\VersionsTab;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class VersionController extends Controller
{
    /** @var ContentService */
    protected $contentService;

    /** @var FormFactory */
    protected $formFactory;

    /**
     * @param ContentService $contentService
     * @param FormFactory $formFactory
     */
    public function __construct(ContentService $contentService, FormFactory $formFactory)
    {
        $this->contentService = $contentService;
        $this->formFactory = $formFactory;
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function removeAction(Request $request): RedirectResponse
    {
        $isDraftForm = null !== $request->get(VersionsTab::FORM_REMOVE_DRAFT);
        $formName = $isDraftForm ? VersionsTab::FORM_REMOVE_DRAFT : VersionsTab::FORM_REMOVE_ARCHIVED;

        $form = $this->formFactory->removeVersion($formName, null, null, null);
        $form->handleRequest($request);

        /** @var UiFormData $uiFormData */
        $uiFormData = $form->getData();
        /** @var VersionRemoveData $versionRemoveData */
        $versionRemoveData = $uiFormData->getData();

        if ($form->isValid() && $form->isSubmitted()) {
            $contentInfo = $versionRemoveData->getContentInfo();

            foreach ($versionRemoveData->getVersions() as $versionNo => $selected) {
                $versionInfo = $this->contentService->loadVersionInfo($contentInfo, $versionNo);
                $this->contentService->deleteVersion($versionInfo);
            }

            $this->flashSuccess('version.remove.success', [
                '%contentName%' => $contentInfo->name,
            ], 'version');

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
}
