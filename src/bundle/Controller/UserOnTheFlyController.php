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
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentCreateStruct;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\Base\Exceptions\UnauthorizedException;
use eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface;
use EzSystems\EzPlatformAdminUi\Form\ActionDispatcher\CreateUserOnTheFlyDispatcher;
use EzSystems\EzPlatformAdminUi\Form\ActionDispatcher\EditUserOnTheFlyDispatcher;
use EzSystems\EzPlatformAdminUi\View\CreateUserOnTheFlyView;
use EzSystems\EzPlatformAdminUi\View\EditContentOnTheFlyView;
use EzSystems\EzPlatformAdminUi\View\EditContentOnTheFlySuccessView;
use EzSystems\EzPlatformContentForms\Data\Mapper\ContentUpdateMapper;
use EzSystems\EzPlatformContentForms\Data\Mapper\UserCreateMapper;
use EzSystems\EzPlatformContentForms\Form\Type\User\UserCreateType;
use EzSystems\EzPlatformContentForms\Form\Type\User\UserUpdateType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserOnTheFlyController extends Controller
{
    /** @var ContentService */
    private $contentService;

    /** @var LanguageService */
    private $languageService;

    /** @var LocationService */
    private $locationService;

    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /** @var \EzSystems\EzPlatformContentForms\Form\ActionDispatcher\UserDispatcher */
    private $userActionDispatcher;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface */
    private $userLanguagePreferenceProvider;

    /** @var \EzSystems\EzPlatformAdminUi\Form\ActionDispatcher\CreateUserOnTheFlyDispatcher */
    private $createUserActionDispatcher;

    /** @var \EzSystems\EzPlatformAdminUi\Form\ActionDispatcher\EditUserOnTheFlyDispatcher */
    private $editUserActionDispatcher;

    public function __construct(
        ContentService $contentService,
        LanguageService $languageService,
        LocationService $locationService,
        UserService $userService,
        ContentTypeService $contentTypeService,
        UserLanguagePreferenceProviderInterface $userLanguagePreferenceProvider,
        CreateUserOnTheFlyDispatcher $createUserActionDispatcher,
        EditUserOnTheFlyDispatcher $editUserActionDispatcher
    ) {
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->languageService = $languageService;
        $this->userService = $userService;
        $this->contentTypeService = $contentTypeService;
        $this->userLanguagePreferenceProvider = $userLanguagePreferenceProvider;
        $this->createUserActionDispatcher = $createUserActionDispatcher;
        $this->editUserActionDispatcher = $editUserActionDispatcher;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $languageCode
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType
     * @param \eZ\Publish\API\Repository\Values\Content\Location $parentLocation
     * @return \eZ\Publish\Core\MVC\Symfony\View\BaseView|\Symfony\Component\HttpFoundation\Response|null
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function createUserAction(
        Request $request,
        string $languageCode,
        ContentType $contentType,
        Location $parentLocation
    ) {
        $language = $this->languageService->loadLanguage($languageCode);
        $parentGroup = $this->userService->loadUserGroup($parentLocation->contentId);

        $data = (new UserCreateMapper())->mapToFormData($contentType, [$parentGroup], [
            'mainLanguageCode' => $language->languageCode,
        ]);
        $form = $this->createForm(UserCreateType::class, $data, [
            'languageCode' => $language->languageCode,
            'mainLanguageCode' => $language->languageCode,
         ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->getClickedButton()) {
            $this->userActionDispatcher->dispatchFormAction($form, $data, $form->getClickedButton()->getName());
            if ($response = $this->userActionDispatcher->getResponse()) {
                return $response;
            }
        }

        return new CreateUserOnTheFlyView('@ezdesign/ui/on_the_fly/user_create_on_the_fly.html.twig', [
            'form' => $form->createView(),
            'language' => $language,
            'content_type' => $contentType,
            'parent_location' => $parentLocation,
        ]);
    }

    /**
     * @param string $languageCode
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType
     * @param \eZ\Publish\API\Repository\Values\Content\Location $parentLocation
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function hasCreateAccessAction(
        string $languageCode,
        ContentType $contentType,
        Location $parentLocation
    ) {
        $response = new JsonResponse();

        try {
            $contentCreateStruct = $this->createContentCreateStruct($parentLocation, $contentType, $languageCode);
            $locationCreateStruct = $this->locationService->newLocationCreateStruct($parentLocation->id);

            $permissionResolver = $this->container->get('ezpublish.api.repository')->getPermissionResolver();

            if (!$permissionResolver->canUser('content', 'create', $contentCreateStruct, [$locationCreateStruct])) {
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

            if (!$permissionResolver->canUser('content', 'publish', $contentCreateStruct, [$locationCreateStruct])) {
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
     * @return \eZ\Publish\Core\MVC\Symfony\View\BaseView|\Symfony\Component\HttpFoundation\Response|null
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function editUserAction(
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

        $language = $this->languageService->loadLanguage($languageCode);

        $contentUpdate = (new ContentUpdateMapper())->mapToFormData($content, [
            'languageCode' => $languageCode,
            'contentType' => $contentType,
        ]);

        $form = $this->createForm(
            UserUpdateType::class,
            $contentUpdate,
            [
                'languageCode' => $languageCode,
                'mainLanguageCode' => $content->contentInfo->mainLanguageCode,
                'drafts_enabled' => true,
            ]
        );

        $form->handleRequest($request);

        if (!$versionInfo->isDraft()) {
            throw new InvalidArgumentException('Version', 'The status is not draft');
        }

        if (null === $location && $content->contentInfo->isPublished()) {
            // assume main location if no location was provided
            $location = $this->locationService->loadLocation((int)$content->mainLocationId);
        }

        if (null !== $location && $location->contentId !== $content->id) {
            throw new InvalidArgumentException('Location', 'The provided Location does not belong to the selected content');
        }

        if ($form->isSubmitted() && $form->isValid() && null !== $form->getClickedButton()) {
            $this->editUserActionDispatcher->dispatchFormAction(
                $form,
                $form->getData(),
                $form->getClickedButton()->getName(),
                ['referrerLocation' => $location]
            );

            if ($response = $this->editUserActionDispatcher->getResponse()) {
                $view = new EditContentOnTheFlySuccessView('@EzPlatformPageBuilder/on_the_fly/content_edit_response.html.twig');
                $view->addParameters([
                    'locationId' => $location->id,
                ]);

                return $view;
            }
        }

        return $this->buildEditView($content, $language, $location, $form, $contentType);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType
     * @param string $language
     *
     * @return \eZ\Publish\API\Repository\Values\Content\ContentCreateStruct
     */
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
        $location,
        FormInterface $form,
        ContentType $contentType
    ): EditContentOnTheFlyView {
        $view = new EditContentOnTheFlyView('@EzPlatformPageBuilder/on_the_fly/user_edit_on_the_fly.html.twig');

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
