<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Content;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Mapper\ContentTranslationMapper;
use EzSystems\RepositoryForms\Content\View\ContentEditView;
use EzSystems\RepositoryForms\Form\ActionDispatcher\ActionDispatcherInterface;
use EzSystems\RepositoryForms\Form\ActionDispatcher\ContentDispatcher;
use EzSystems\RepositoryForms\Form\Type\Content\ContentEditType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentEditController extends Controller
{
    /** @var ContentService */
    private $contentService;

    /** @var ContentTypeService */
    private $contentTypeService;

    /** @var LanguageService */
    private $languageService;

    /** @var ActionDispatcherInterface */
    private $contentActionDispatcher;

    /** @var LocationService */
    private $locationService;

    /**
     * @param ContentService $contentService
     * @param ContentTypeService $contentTypeService
     * @param LanguageService $languageService
     * @param ContentDispatcher $contentActionDispatcher
     * @param LocationService $locationService
     */
    public function __construct(
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        LanguageService $languageService,
        ContentDispatcher $contentActionDispatcher,
        LocationService $locationService
    ) {
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->languageService = $languageService;
        $this->contentActionDispatcher = $contentActionDispatcher;
        $this->locationService = $locationService;
    }

    /**
     * @param Content $content
     * @param string|null $fromLanguageCode
     * @param string|null $toLanguageCode
     * @param Request $request
     * @param int|null $locationId
     *
     * @return ContentEditView|Response
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function translateAction(
        Content $content,
        ?string $fromLanguageCode,
        ?string $toLanguageCode,
        Request $request,
        ?int $locationId = null
    ) {
        /* @todo could be improved with ParamConverters */
        $fromLanguage = null === $fromLanguageCode ? null : $this->languageService->loadLanguage($fromLanguageCode);
        $toLanguage = $this->languageService->loadLanguage($toLanguageCode);
        $contentType = $this->contentTypeService->loadContentType($content->contentInfo->contentTypeId);
        $contentInfo = $content->contentInfo;
        $location = $this->locationService->loadLocation($locationId ?? $contentInfo->mainLocationId);

        $contentUpdate = (new ContentTranslationMapper())->mapToFormData(
            $content,
            [
                'language' => $toLanguage,
                'baseLanguage' => $fromLanguage,
                'contentType' => $contentType,
            ]
        );
        $form = $this->createForm(
            ContentEditType::class,
            $contentUpdate,
            [
                'languageCode' => $toLanguage->languageCode,
                'mainLanguageCode' => $content->contentInfo->mainLanguageCode,
                'drafts_enabled' => true,
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->contentActionDispatcher->dispatchFormAction(
                $form,
                $contentUpdate,
                $form->getClickedButton()->getName(),
                ['referrerLocation' => $location]
            );
            if ($response = $this->contentActionDispatcher->getResponse()) {
                return $response;
            }
        }

        return new ContentEditView('@EzPlatformAdminUi/content/content_edit/content_edit.html.twig', [
            'form' => $form->createView(),
            'location' => $location,
            'language' => $toLanguage,
            'baseLanguage' => $fromLanguage ?: null,
            'content' => $this->contentService->loadContentByContentInfo($contentInfo, [$contentInfo->mainLanguageCode]),
            'contentType' => $contentType,
        ]);
    }
}
