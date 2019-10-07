<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use EzSystems\EzPlatformAdminUi\Form\ActionDispatcher\ActionDispatcherInterface;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Content\CreateContentDraftData;
use EzSystems\EzPlatformAdminUi\View\ContentCreateSuccessView;
use EzSystems\EzPlatformAdminUi\View\ContentCreateView;
use EzSystems\EzPlatformAdminUi\View\ContentEditSuccessView;
use EzSystems\EzPlatformAdminUi\View\ContentEditView;
use EzSystems\EzPlatformAdminUi\View\ContentTranslateSuccessView;
use EzSystems\EzPlatformAdminUi\View\ContentTranslateView;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\ContentDraftCreateType;
use Symfony\Component\HttpFoundation\Request;

class ContentEditController extends Controller
{
    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \EzSystems\EzPlatformAdminUi\Form\ActionDispatcher\ActionDispatcherInterface */
    private $contentActionDispatcher;

    public function __construct(
        ContentTypeService $contentTypeService,
        ContentService $contentService,
        ActionDispatcherInterface $contentActionDispatcher
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->contentService = $contentService;
        $this->contentActionDispatcher = $contentActionDispatcher;
    }

    /**
     * Displays and processes a content creation form. Showing the form does not create a draft in the repository.
     *
     * @param \EzSystems\EzPlatformAdminUi\View\ContentCreateView $view
     *
     * @return \EzSystems\EzPlatformAdminUi\View\ContentCreateView
     */
    public function createWithoutDraftAction(ContentCreateView $view): ContentCreateView
    {
        return $view;
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\View\ContentCreateSuccessView $view
     *
     * @return \EzSystems\EzPlatformAdminUi\View\ContentCreateSuccessView
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     */
    public function createWithoutDraftSuccessAction(ContentCreateSuccessView $view): ContentCreateSuccessView
    {
        return $view;
    }

    /**
     * Displays a draft creation form that creates a content draft from an existing content.
     *
     * @param mixed $contentId
     * @param int $fromVersionNo
     * @param string $fromLanguage
     * @param string $toLanguage
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \EzSystems\EzPlatformAdminUi\View\ContentCreateDraftView|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    public function createContentDraftAction(
        $contentId,
        $fromVersionNo = null,
        $fromLanguage = null,
        $toLanguage = null,
        Request $request
    ) {
        $createContentDraft = new CreateContentDraftData();
        $contentInfo = null;
        $contentType = null;

        if ($contentId !== null) {
            $createContentDraft->contentId = $contentId;

            $contentInfo = $this->contentService->loadContentInfo($contentId);
            $contentType = $this->contentTypeService->loadContentType($contentInfo->contentTypeId);
            $createContentDraft->fromVersionNo = $fromVersionNo ?: $contentInfo->currentVersionNo;
            $createContentDraft->fromLanguage = $fromLanguage ?: $contentInfo->mainLanguageCode;
        }

        $form = $this->createForm(
            ContentDraftCreateType::class,
            $createContentDraft,
            [
                'action' => $this->generateUrl('ezplatform.content.draft.create'),
            ]
        );

        $form->handleRequest($request);

        if ($form->isValid() && null !== $form->getClickedButton()) {
            $this->contentActionDispatcher->dispatchFormAction($form, $createContentDraft, $form->getClickedButton()->getName());
            if ($response = $this->contentActionDispatcher->getResponse()) {
                return $response;
            }
        }

        return new ContentCreateDraftView(null, [
            'form' => $form->createView(),
            'contentInfo' => $contentInfo,
            'contentType' => $contentType,
        ]);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\View\ContentEditView $view
     *
     * @return \EzSystems\EzPlatformAdminUi\View\ContentEditView
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     */
    public function editVersionDraftAction(ContentEditView $view): ContentEditView
    {
        return $view;
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\View\ContentEditSuccessView $view
     *
     * @return \EzSystems\EzPlatformAdminUi\View\ContentEditSuccessView
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     */
    public function editVersionDraftSuccessAction(ContentEditSuccessView $view): ContentEditSuccessView
    {
        return $view;
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\View\ContentTranslateView $view
     *
     * @return \EzSystems\EzPlatformAdminUi\View\ContentTranslateView
     */
    public function translateAction(ContentTranslateView $view): ContentTranslateView
    {
        return $view;
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\View\ContentTranslateSuccessView $view
     *
     * @return \EzSystems\EzPlatformAdminUi\View\ContentTranslateSuccessView
     */
    public function translationSuccessAction(ContentTranslateSuccessView $view): ContentTranslateSuccessView
    {
        return $view;
    }
}
