<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions as ApiException;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentCreateStruct;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Base\Exceptions\BadStateException;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\Base\Exceptions\UnauthorizedException;
use eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface;
use EzSystems\EzPlatformAdminUi\Form\ActionDispatcher\CreateContentOnTheFlyDispatcher;
use EzSystems\EzPlatformAdminUi\Form\ActionDispatcher\EditContentOnTheFlyDispatcher;
use EzSystems\EzPlatformAdminUi\Specification\ContentType\ContentTypeIsUser;
use EzSystems\EzPlatformAdminUi\View\CreateContentOnTheFlyView;
use EzSystems\EzPlatformAdminUi\View\EditContentOnTheFlyView;
use EzSystems\EzPlatformAdminUi\View\EditContentOnTheFlySuccessView;
use EzSystems\EzPlatformContentForms\Data\Mapper\ContentCreateMapper;
use EzSystems\EzPlatformContentForms\Data\Mapper\ContentUpdateMapper;
use EzSystems\EzPlatformContentForms\Form\Type\Content\ContentEditType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ContentOnTheFlyController extends Controller
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\API\Repository\LanguageService */
    private $languageService;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface */
    private $userLanguagePreferenceProvider;

    /** @var \EzSystems\EzPlatformAdminUi\Form\ActionDispatcher\CreateContentOnTheFlyDispatcher */
    private $createContentActionDispatcher;

    /** @var \EzSystems\EzPlatformAdminUi\Form\ActionDispatcher\EditContentOnTheFlyDispatcher */
    private $editContentActionDispatcher;

    /** @var string[] */
    private $userContentTypeIdentifiers;

    /**
     * @param string[] $userContentTypeIdentifiers
     */
    public function __construct(
        ContentService $contentService,
        LanguageService $languageService,
        LocationService $locationService,
        ContentTypeService $contentTypeService,
        PermissionResolver $permissionResolver,
        UserLanguagePreferenceProviderInterface $userLanguagePreferenceProvider,
        CreateContentOnTheFlyDispatcher $createContentActionDispatcher,
        EditContentOnTheFlyDispatcher $editContentActionDispatcher,
        array $userContentTypeIdentifiers
    ) {
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->languageService = $languageService;
        $this->contentTypeService = $contentTypeService;
        $this->permissionResolver = $permissionResolver;
        $this->userLanguagePreferenceProvider = $userLanguagePreferenceProvider;
        $this->createContentActionDispatcher = $createContentActionDispatcher;
        $this->editContentActionDispatcher = $editContentActionDispatcher;
        $this->userContentTypeIdentifiers = $userContentTypeIdentifiers;
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function hasCreateAccessAction(
        string $languageCode,
        ContentType $contentType,
        Location $parentLocation
    ): JsonResponse {
        $response = new JsonResponse();

        try {
            $contentCreateStruct = $this->createContentCreateStruct($parentLocation, $contentType, $languageCode);
            $locationCreateStruct = $this->locationService->newLocationCreateStruct($parentLocation->id);

            if (!$this->permissionResolver->canUser('content', 'create', $contentCreateStruct, [$locationCreateStruct])) {
                throw new UnauthorizedException(
                    'content',
                    'create',
                    [
                        'contentTypeIdentifier' => $contentType->identifier,
                        'parentLocationId' => $locationCreateStruct->parentLocationId,
                        'languageCode' => $languageCode,
                    ]
                );
            }

            if (!$this->permissionResolver->canUser('content', 'publish', $contentCreateStruct, [$locationCreateStruct])) {
                throw new UnauthorizedException(
                    'content',
                    'publish',
                    [
                        'contentTypeIdentifier' => $contentType->identifier,
                        'parentLocationId' => $locationCreateStruct->parentLocationId,
                        'languageCode' => $languageCode,
                    ]
                );
            }

            $response->setData([
                'access' => true,
            ]);
        } catch (ApiException\UnauthorizedException $exception) {
            $response->setData([
                'access' => false,
                'message' => $exception->getMessage(),
            ]);
        }

        return $response;
    }

    /**
     * @return \eZ\Publish\Core\MVC\Symfony\View\BaseView|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function createContentAction(
        Request $request,
        string $languageCode,
        ContentType $contentType,
        Location $parentLocation
    ) {
        if ((new ContentTypeIsUser($this->userContentTypeIdentifiers))->isSatisfiedBy($contentType)) {
            return $this->forward('EzSystems\EzPlatformAdminUiBundle\Controller\UserOnTheFlyController::createUserAction', [
                'languageCode' => $languageCode,
                'contentType' => $contentType,
                'parentLocation' => $parentLocation,
            ]);
        }

        $language = $this->languageService->loadLanguage($languageCode);

        $data = (new ContentCreateMapper())->mapToFormData($contentType, [
            'mainLanguageCode' => $language->languageCode,
            'parentLocation' => $this->locationService->newLocationCreateStruct($parentLocation->id),
        ]);

        $form = $this->createForm(ContentEditType::class, $data, [
            'languageCode' => $language->languageCode,
            'mainLanguageCode' => $language->languageCode,
            'drafts_enabled' => false,
            'intent' => 'create',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->getClickedButton()) {
            $this->createContentActionDispatcher->dispatchFormAction($form, $data, $form->getClickedButton()->getName());
            if ($response = $this->createContentActionDispatcher->getResponse()) {
                return $response;
            }
        }

        return new CreateContentOnTheFlyView('@ezdesign/ui/on_the_fly/content_create_on_the_fly.html.twig', [
            'form' => $form->createView(),
            'language' => $language,
            'content_type' => $contentType,
            'parent_location' => $parentLocation,
        ]);
    }

    /**
     * @return \eZ\Publish\Core\MVC\Symfony\View\BaseView|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\Core\Base\Exceptions\BadStateException
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException
     */
    public function editContentAction(
        Request $request,
        string $languageCode,
        int $contentId,
        int $versionNo,
        int $locationId
    ) {
        $content = $this->contentService->loadContent($contentId, [$languageCode], $versionNo);
        $versionInfo = $content->getVersionInfo();
        $location = $this->locationService->loadLocation($locationId);

        $contentType = $this->contentTypeService->loadContentType(
            $content->contentInfo->contentTypeId,
            $this->userLanguagePreferenceProvider->getPreferredLanguages()
        );

        if ((new ContentTypeIsUser($this->userContentTypeIdentifiers))->isSatisfiedBy($contentType)) {
            return $this->forward('EzSystems\EzPlatformAdminUiBundle\Controller\UserOnTheFlyController::editUserAction', [
                'languageCode' => $languageCode,
                'contentId' => $contentId,
                'versionNo' => $versionNo,
                'locationId' => $locationId,
            ]);
        }

        $language = $this->languageService->loadLanguage($languageCode);

        $contentUpdate = (new ContentUpdateMapper())->mapToFormData($content, [
            'languageCode' => $languageCode,
            'contentType' => $contentType,
        ]);

        $form = $this->createForm(
            ContentEditType::class,
            $contentUpdate,
            [
                'languageCode' => $languageCode,
                'mainLanguageCode' => $content->contentInfo->mainLanguageCode,
                'drafts_enabled' => true,
            ]
        );

        $form->handleRequest($request);

        if (!$versionInfo->isDraft()) {
            throw new BadStateException('Version', 'The status is not draft');
        }

        if (null === $location && $content->contentInfo->isPublished()) {
            // assume main location if no location was provided
            $location = $content->contentInfo->getMainLocation();
        }

        if (null !== $location && $location->contentId !== $content->id) {
            throw new InvalidArgumentException('Location', 'The provided Location does not belong to the selected content');
        }

        if ($form->isSubmitted() && $form->isValid() && null !== $form->getClickedButton()) {
            $this->createContentActionDispatcher->dispatchFormAction(
                $form,
                $form->getData(),
                $form->getClickedButton()->getName(),
                ['referrerLocation' => $location]
            );

            if ($this->createContentActionDispatcher->getResponse()) {
                $view = new EditContentOnTheFlySuccessView('@ezdesign/ui/on_the_fly/content_edit_response.html.twig');
                $view->addParameters([
                    'locationId' => $location->id,
                ]);

                return $view;
            }
        }

        return $this->buildEditView($content, $language, $location, $form, $contentType);
    }

    private function createContentCreateStruct(
        Location $location,
        ContentType $contentType,
        string $language
    ): ContentCreateStruct {
        $contentCreateStruct = $this->contentService->newContentCreateStruct($contentType, $language);
        $contentCreateStruct->sectionId = $location->contentInfo->sectionId;

        return $contentCreateStruct;
    }

    private function buildEditView(
        Content $content,
        Language $language,
        Location $location,
        FormInterface $form,
        ContentType $contentType
    ): EditContentOnTheFlyView {
        $view = new EditContentOnTheFlyView('@ezdesign/ui/on_the_fly/content_edit_on_the_fly.html.twig');

        $view->setContent($content);
        $view->setLanguage($language);
        $view->setLocation($location);
        $view->setForm($form);

        $view->addParameters([
            'content' => $content,
            'location' => $location,
            'language' => $language,
            'content_type' => $contentType,
            'form' => $form->createView(),
        ]);

        return $view;
    }
}
