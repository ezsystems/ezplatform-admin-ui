<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller\User;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\Core\Base\Exceptions\UnauthorizedException as CoreUnauthorizedException;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Mapper\UserCreateMapper;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Mapper\UserUpdateMapper;
use EzSystems\EzPlatformAdminUi\View\UserCreateView;
use EzSystems\EzPlatformAdminUi\View\UserUpdateView;
use EzSystems\EzPlatformAdminUi\Form\ActionDispatcher\ActionDispatcherInterface;
use EzSystems\EzPlatformAdminUi\Form\Type\User\UserCreateType;
use EzSystems\EzPlatformAdminUi\Form\Type\User\UserUpdateType;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \eZ\Publish\API\Repository\LanguageService */
    private $languageService;

    /** @var \EzSystems\EzPlatformAdminUi\Form\ActionDispatcher\ActionDispatcherInterface */
    private $userActionDispatcher;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    public function __construct(
        ContentTypeService $contentTypeService,
        UserService $userService,
        LocationService $locationService,
        LanguageService $languageService,
        ActionDispatcherInterface $userActionDispatcher,
        PermissionResolver $permissionResolver
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->userService = $userService;
        $this->locationService = $locationService;
        $this->languageService = $languageService;
        $this->userActionDispatcher = $userActionDispatcher;
        $this->permissionResolver = $permissionResolver;
    }

    /**
     * Displays and processes a user creation form.
     *
     * @param string $contentTypeIdentifier ContentType id to create
     * @param string $language Language code to create the content in (eng-GB, ger-DE, ...))
     * @param int $parentLocationId Location the content should be a child of
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \EzSystems\EzPlatformAdminUi\View\UserCreateView|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\OptionDefinitionException
     * @throws \Symfony\Component\OptionsResolver\Exception\NoSuchOptionException
     * @throws \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function createAction(
        string $contentTypeIdentifier,
        string $language,
        int $parentLocationId,
        Request $request
    ) {
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);
        $location = $this->locationService->loadLocation($parentLocationId);
        $language = $this->languageService->loadLanguage($language);
        $parentGroup = $this->userService->loadUserGroup($location->contentId);

        $data = (new UserCreateMapper())->mapToFormData($contentType, [$parentGroup], [
            'mainLanguageCode' => $language->languageCode,
        ]);
        $form = $this->createForm(UserCreateType::class, $data, [
            'languageCode' => $language->languageCode,
            'mainLanguageCode' => $language->languageCode,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && null !== $form->getClickedButton()) {
            $this->userActionDispatcher->dispatchFormAction($form, $data, $form->getClickedButton()->getName());
            if ($response = $this->userActionDispatcher->getResponse()) {
                return $response;
            }
        }

        $userCreateView = new UserCreateView(null, [
            'form' => $form->createView(),
            'language' => $language,
            'content_type' => $contentType,
            'parent_group' => $parentGroup,
        ]);

        return $userCreateView;
    }

    /**
     * Displays a user update form that updates user data and related content item.
     *
     * @param int|null $contentId ContentType id to create
     * @param int|null $versionNo Version number the version should be created from. Defaults to the currently published one.
     * @param string|null $language Language code to create the version in (eng-GB, ger-DE, ...))
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \EzSystems\EzPlatformAdminUi\View\UserUpdateView|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     * @throws \eZ\Publish\Core\Base\Exceptions\UnauthorizedException
     */
    public function editAction(
        int $contentId,
        ?int $versionNo = null,
        ?string $language = null,
        Request $request
    ) {
        $user = $this->userService->loadUser($contentId);
        if (!$this->permissionResolver->canUser('content', 'edit', $user)) {
            throw new CoreUnauthorizedException('content', 'edit', ['userId' => $contentId]);
        }
        $contentType = $this->contentTypeService->loadContentType($user->contentInfo->contentTypeId);

        $userUpdate = (new UserUpdateMapper())->mapToFormData($user, $contentType, [
            'languageCode' => $language,
        ]);
        $form = $this->createForm(
            UserUpdateType::class,
            $userUpdate,
            [
                'languageCode' => $language,
                'mainLanguageCode' => $user->contentInfo->mainLanguageCode,
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && null !== $form->getClickedButton()) {
            $this->userActionDispatcher->dispatchFormAction($form, $userUpdate, $form->getClickedButton()->getName());
            if ($response = $this->userActionDispatcher->getResponse()) {
                return $response;
            }
        }

        return new UserUpdateView(null, [
            'form' => $form->createView(),
            'language_code' => $language,
            'content_type' => $contentType,
            'user' => $user,
        ]);
    }
}
