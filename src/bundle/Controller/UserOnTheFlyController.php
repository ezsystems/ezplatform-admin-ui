<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions as ApiException;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentCreateStruct;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\Base\Exceptions\UnauthorizedException;
use eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface;
use eZ\Publish\Core\MVC\Symfony\View\BaseView;
use EzSystems\EzPlatformAdminUi\Form\ActionDispatcher\CreateUserOnTheFlyDispatcher;
use EzSystems\EzPlatformAdminUi\Form\ActionDispatcher\EditUserOnTheFlyDispatcher;
use EzSystems\EzPlatformAdminUi\View\CreateUserOnTheFlyView;
use EzSystems\EzPlatformAdminUi\View\EditContentOnTheFlySuccessView;
use EzSystems\EzPlatformAdminUi\View\EditUserOnTheFlyView;
use EzSystems\EzPlatformContentForms\Data\Mapper\UserCreateMapper;
use EzSystems\EzPlatformContentForms\Data\Mapper\UserUpdateMapper;
use EzSystems\EzPlatformContentForms\Form\Type\User\UserCreateType;
use EzSystems\EzPlatformContentForms\Form\Type\User\UserUpdateType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserOnTheFlyController extends Controller
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\API\Repository\LanguageService */
    private $languageService;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

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
        PermissionResolver $permissionResolver,
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
        $this->permissionResolver = $permissionResolver;
    }

    /**
     * @return \eZ\Publish\Core\MVC\Symfony\View\BaseView|\Symfony\Component\HttpFoundation\Response
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
            $this->createUserActionDispatcher->dispatchFormAction($form, $data, $form->getClickedButton()->getName());
            if ($response = $this->createUserActionDispatcher->getResponse()) {
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

    public function hasCreateAccessAction(
        string $languageCode,
        ContentType $contentType,
        Location $parentLocation
    ): JsonResponse {
        $response = new JsonResponse();

        try {
            $userCreateStruct = $this->createContentCreateStruct($contentType, $languageCode);
            $locationCreateStruct = $this->locationService->newLocationCreateStruct($parentLocation->id);

            if (!$this->permissionResolver->canUser('content', 'create', $userCreateStruct, [$locationCreateStruct])) {
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

            if (!$this->permissionResolver->canUser('content', 'publish', $userCreateStruct, [$locationCreateStruct])) {
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
    ): BaseView {
        $user = $this->userService->loadUser($contentId, [$languageCode]);
        $location = $this->locationService->loadLocation($locationId);
        $contentType = $user->getContentType();
        $language = $this->languageService->loadLanguage($languageCode);

        $contentUpdate = (new UserUpdateMapper())->mapToFormData($user, $contentType, [
            'languageCode' => $languageCode,
        ]);

        $form = $this->createForm(
            UserUpdateType::class,
            $contentUpdate,
            [
                'languageCode' => $languageCode,
                'mainLanguageCode' => $user->contentInfo->mainLanguageCode,
            ]
        );

        $form->handleRequest($request);

        if (null === $location && $user->contentInfo->isPublished()) {
            // assume main location if no location was provided
            $location = $user->contentInfo->getMainLocation();
        }

        if (null !== $location && $location->contentId !== $user->id) {
            throw new InvalidArgumentException('Location', 'The provided Location does not belong to the selected content');
        }

        if ($form->isSubmitted() && $form->isValid() && null !== $form->getClickedButton()) {
            $this->editUserActionDispatcher->dispatchFormAction(
                $form,
                $form->getData(),
                $form->getClickedButton()->getName(),
                ['referrerLocation' => $location]
            );

            if ($this->editUserActionDispatcher->getResponse()) {
                $view = new EditContentOnTheFlySuccessView('@ezdesign/ui/on_the_fly/user_edit_response.html.twig');
                $view->addParameters([
                    'locationId' => $location->id,
                ]);

                return $view;
            }
        }

        return $this->buildEditView($user, $language, $location, $form, $contentType);
    }

    private function createContentCreateStruct(
        ContentType $contentType,
        string $language
    ): ContentCreateStruct {
        return $this->userService->newUserCreateStruct(
            'permission_check',
            'permission_check',
            'permission_check',
            $language,
            $contentType
        );
    }

    private function buildEditView(
        Content $content,
        Language $language,
        ?Location $location,
        FormInterface $form,
        ContentType $contentType
    ): EditUserOnTheFlyView {
        $view = new EditUserOnTheFlyView('@ezdesign/ui/on_the_fly/user_edit_on_the_fly.html.twig');

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
