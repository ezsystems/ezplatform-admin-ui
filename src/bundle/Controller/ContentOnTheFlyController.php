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
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentCreateStruct;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Base\Exceptions\BadStateException;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\Base\Exceptions\UnauthorizedException;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface;
use EzSystems\EzPlatformAdminUi\Event\ContentProxyCreateEvent;
use EzSystems\EzPlatformAdminUi\Event\Options;
use EzSystems\EzPlatformAdminUi\Form\ActionDispatcher\CreateContentOnTheFlyDispatcher;
use EzSystems\EzPlatformAdminUi\Specification\ContentType\ContentTypeIsUser;
use EzSystems\EzPlatformAdminUi\View\CreateContentOnTheFlyView;
use EzSystems\EzPlatformAdminUi\View\EditContentOnTheFlySuccessView;
use EzSystems\EzPlatformAdminUi\View\EditContentOnTheFlyView;
use EzSystems\EzPlatformContentForms\Data\Mapper\ContentCreateMapper;
use EzSystems\EzPlatformContentForms\Data\Mapper\ContentUpdateMapper;
use EzSystems\EzPlatformContentForms\Form\ActionDispatcher\ActionDispatcherInterface;
use EzSystems\EzPlatformContentForms\Form\Type\Content\ContentEditType;
use Ibexa\Contracts\ContentForms\Content\Form\Provider\GroupedContentFormFieldsProviderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ContentOnTheFlyController extends Controller
{
    private const AUTOSAVE_ACTION_NAME = 'autosave';

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

    /** @var \Ibexa\Contracts\ContentForms\Content\Form\Provider\GroupedContentFormFieldsProviderInterface */
    private $groupedContentFormFieldsProvider;

    /** @var \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface */
    private $userLanguagePreferenceProvider;

    /** @var \EzSystems\EzPlatformAdminUi\Form\ActionDispatcher\CreateContentOnTheFlyDispatcher */
    private $createContentActionDispatcher;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface */
    private $eventDispatcher;

    /** @var \EzSystems\EzPlatformContentForms\Form\ActionDispatcher\ActionDispatcherInterface */
    private $contentActionDispatcher;

    public function __construct(
        ContentService $contentService,
        LanguageService $languageService,
        LocationService $locationService,
        ContentTypeService $contentTypeService,
        PermissionResolver $permissionResolver,
        GroupedContentFormFieldsProviderInterface $groupedContentFormFieldsProvider,
        UserLanguagePreferenceProviderInterface $userLanguagePreferenceProvider,
        CreateContentOnTheFlyDispatcher $createContentActionDispatcher,
        ConfigResolverInterface $configResolver,
        EventDispatcherInterface $eventDispatcher,
        ActionDispatcherInterface $contentActionDispatcher
    ) {
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->languageService = $languageService;
        $this->contentTypeService = $contentTypeService;
        $this->permissionResolver = $permissionResolver;
        $this->groupedContentFormFieldsProvider = $groupedContentFormFieldsProvider;
        $this->userLanguagePreferenceProvider = $userLanguagePreferenceProvider;
        $this->createContentActionDispatcher = $createContentActionDispatcher;
        $this->configResolver = $configResolver;
        $this->eventDispatcher = $eventDispatcher;
        $this->contentActionDispatcher = $contentActionDispatcher;
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
        if ((new ContentTypeIsUser($this->configResolver->getParameter('user_content_type_identifier')))->isSatisfiedBy($contentType)) {
            return $this->forward('EzSystems\EzPlatformAdminUiBundle\Controller\UserOnTheFlyController::createUserAction', [
                'languageCode' => $languageCode,
                'contentType' => $contentType,
                'parentLocation' => $parentLocation,
            ]);
        }

        /** @var \EzSystems\EzPlatformAdminUi\Event\ContentProxyCreateEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new ContentProxyCreateEvent(
                $contentType,
                $languageCode,
                $parentLocation->id,
                new Options([
                    'isOnTheFly' => true,
                ])
            )
        );

        if ($event->hasResponse()) {
            return $event->getResponse();
        }

        $language = $this->languageService->loadLanguage($languageCode);

        $data = (new ContentCreateMapper())->mapToFormData($contentType, [
            'mainLanguageCode' => $language->languageCode,
            'parentLocation' => $this->locationService->newLocationCreateStruct($parentLocation->id),
        ]);

        $form = $this->createForm(ContentEditType::class, $data, [
            'languageCode' => $language->languageCode,
            'mainLanguageCode' => $language->languageCode,
            'contentCreateStruct' => $data,
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
            'grouped_fields' => $this->groupedContentFormFieldsProvider->getGroupedFields(
                $form->get('fieldsData')->all()
            ),
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
        ?int $locationId
    ) {
        $content = $this->contentService->loadContent($contentId, [$languageCode], $versionNo);
        $versionInfo = $content->getVersionInfo();

        $location = null;
        if (!empty($locationId)) {
            $location = $this->locationService->loadLocation($locationId);
        }

        $contentType = $this->contentTypeService->loadContentType(
            $content->contentInfo->contentTypeId,
            $this->userLanguagePreferenceProvider->getPreferredLanguages()
        );

        if ((new ContentTypeIsUser($this->configResolver->getParameter('user_content_type_identifier')))->isSatisfiedBy($contentType)) {
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
                'content' => $content,
                'contentUpdateStruct' => $contentUpdate,
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
            $actionName = $form->getClickedButton()->getName();

            $actionDispatcher = $actionName === self::AUTOSAVE_ACTION_NAME
                ? $this->contentActionDispatcher
                : $this->createContentActionDispatcher;

            $actionDispatcher->dispatchFormAction(
                $form,
                $form->getData(),
                $actionName,
                ['referrerLocation' => $location]
            );

            if (!$location instanceof Location) {
                $contentInfo = $this->contentService->loadContentInfo($content->id);

                if (null !== $contentInfo->mainLocationId) {
                    $location = $this->locationService->loadLocation($contentInfo->mainLocationId);
                }
            }

            if ($actionDispatcher->getResponse()) {
                $view = new EditContentOnTheFlySuccessView('@ezdesign/ui/on_the_fly/content_edit_response.html.twig');
                $view->addParameters([
                    'locationId' => $location instanceof Location ? $location->id : null,
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
        ?Location $location,
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
            'grouped_fields' => $this->groupedContentFormFieldsProvider->getGroupedFields(
                $form->get('fieldsData')->all()
            ),
        ]);

        return $view;
    }
}
