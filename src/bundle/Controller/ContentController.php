<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions as ApiException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\User\Limitation;
use eZ\Publish\Core\Base\Exceptions\BadStateException;
use eZ\Publish\Core\Helper\TranslationHelper;
use eZ\Publish\SPI\Limitation\Target;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException as AdminInvalidArgumentException;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\ContentVisibilityUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentEditData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Location\ContentMainLocationUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Translation\MainTranslationUpdateData;
use EzSystems\EzPlatformAdminUi\Form\DataMapper\ContentMainLocationUpdateMapper;
use EzSystems\EzPlatformAdminUi\Form\DataMapper\MainTranslationUpdateMapper;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\ContentVisibilityUpdateType;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\Translation\MainTranslationUpdateType;
use EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface;
use EzSystems\EzPlatformAdminUi\Permission\LookupLimitationsTransformer;
use EzSystems\EzPlatformAdminUi\Siteaccess\SiteaccessResolverInterface;
use EzSystems\EzPlatformAdminUi\Specification\ContentIsUser;
use EzSystems\EzPlatformAdminUi\Specification\ContentType\ContentTypeIsUser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\Translation\Exception\InvalidArgumentException;
use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException as APIRepositoryInvalidArgumentException;
use Symfony\Component\Translation\Exception\InvalidArgumentException as TranslationInvalidArgumentException;

class ContentController extends Controller
{
    /** @var TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    /** @var ContentService */
    private $contentService;

    /** @var FormFactory */
    private $formFactory;

    /** @var SubmitHandler */
    private $submitHandler;

    /** @var ContentMainLocationUpdateMapper */
    private $contentMainLocationUpdateMapper;

    /** @var SiteaccessResolverInterface */
    private $siteaccessResolver;

    /** @var LocationService */
    private $locationService;

    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \EzSystems\EzPlatformAdminUi\Permission\LookupLimitationsTransformer */
    private $lookupLimitationsTransformer;

    /** @var \eZ\Publish\Core\Helper\TranslationHelper */
    private $translationHelper;

    /** @var array */
    private $userContentTypeIdentifier;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /**
     * @param \EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface $notificationHandler
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory $formFactory
     * @param \EzSystems\EzPlatformAdminUi\Form\SubmitHandler $submitHandler
     * @param \EzSystems\EzPlatformAdminUi\Form\DataMapper\ContentMainLocationUpdateMapper $contentMetadataUpdateMapper
     * @param \EzSystems\EzPlatformAdminUi\Siteaccess\SiteaccessResolverInterface $siteaccessResolver
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \eZ\Publish\API\Repository\UserService $userService
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \EzSystems\EzPlatformAdminUi\Permission\LookupLimitationsTransformer $lookupLimitationsTransformer
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param array $userContentTypeIdentifier
     */
    public function __construct(
        TranslatableNotificationHandlerInterface $notificationHandler,
        ContentService $contentService,
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        ContentMainLocationUpdateMapper $contentMetadataUpdateMapper,
        SiteaccessResolverInterface $siteaccessResolver,
        LocationService $locationService,
        UserService $userService,
        PermissionResolver $permissionResolver,
        LookupLimitationsTransformer $lookupLimitationsTransformer,
        TranslationHelper $translationHelper,
        ContentTypeService $contentTypeService,
        array $userContentTypeIdentifier
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->contentService = $contentService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->contentMainLocationUpdateMapper = $contentMetadataUpdateMapper;
        $this->siteaccessResolver = $siteaccessResolver;
        $this->locationService = $locationService;
        $this->userService = $userService;
        $this->userContentTypeIdentifier = $userContentTypeIdentifier;
        $this->permissionResolver = $permissionResolver;
        $this->translationHelper = $translationHelper;
        $this->lookupLimitationsTransformer = $lookupLimitationsTransformer;
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws InvalidOptionsException
     * @throws InvalidArgumentException
     * @throws ApiException\UnauthorizedException
     * @throws ApiException\InvalidArgumentException
     * @throws ApiException\ContentValidationException
     * @throws ApiException\ContentFieldValidationException
     */
    public function createAction(Request $request): Response
    {
        $form = $this->formFactory->createContent();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (ContentCreateData $data) {
                $contentType = $data->getContentType();
                $language = $data->getLanguage();
                $parentLocation = $data->getParentLocation();

                if ((new ContentTypeIsUser($this->userContentTypeIdentifier))->isSatisfiedBy($contentType)) {
                    return $this->redirectToRoute('ezplatform.user.create', [
                        'contentTypeIdentifier' => $contentType->identifier,
                        'language' => $language->languageCode,
                        'parentLocationId' => $parentLocation->id,
                    ]);
                }

                return $this->redirectToRoute('ezplatform.content.create_no_draft', [
                    'contentTypeIdentifier' => $contentType->identifier,
                    'language' => $language->languageCode,
                    'parentLocationId' => $parentLocation->id,
                ]);
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('ezplatform.dashboard'));
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws InvalidArgumentException
     * @throws UnauthorizedException
     * @throws InvalidOptionsException
     */
    public function editAction(Request $request): Response
    {
        /* @todo it shouldn't rely on keys from request */
        $requestKeys = $request->request->keys();
        $formName = reset($requestKeys) ?: null;

        $form = $this->formFactory->contentEdit(null, $formName);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (ContentEditData $data) {
                $contentInfo = $data->getContentInfo();
                $language = $data->getLanguage();
                $location = $data->getLocation();

                $content = $this->contentService->loadContent($contentInfo->id);
                $versionInfo = $data->getVersionInfo() ?? $content->getVersionInfo();
                $versionNo = $versionInfo->versionNo;

                if ((new ContentIsUser($this->userService))->isSatisfiedBy($content)) {
                    return $this->redirectToRoute('ezplatform.user.update', [
                        'contentId' => $contentInfo->id,
                        'versionNo' => $versionNo,
                        'language' => $language->languageCode,
                    ]);
                }

                if (!$versionInfo->isDraft()) {
                    $contentDraft = $this->contentService->createContentDraft($contentInfo, $versionInfo, null, $language);
                    $versionNo = $contentDraft->getVersionInfo()->versionNo;

                    $this->notificationHandler->success(
                        /** @Desc("Created a new draft for '%name%'.") */
                        'content.create_draft.success',
                        ['%name%' => $this->translationHelper->getTranslatedContentName($content)],
                        'content'
                    );
                }

                return $this->redirectToRoute('ezplatform.content.draft.edit', [
                    'contentId' => $contentInfo->id,
                    'versionNo' => $versionNo,
                    'language' => $language->languageCode,
                    'locationId' => null !== $location
                        ? $location->id
                        : $contentInfo->mainLocationId,
                ]);
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        /** @var ContentEditData $data */
        $data = $form->getData();
        $contentInfo = $data->getContentInfo();

        if (null !== $contentInfo) {
            return $this->redirectToRoute('_ez_content_view', [
                'contentId' => $contentInfo->id,
                'locationId' => $contentInfo->mainLocationId,
            ]);
        }

        return $this->redirectToRoute('ezplatform.dashboard');
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws AdminInvalidArgumentException
     * @throws \InvalidArgumentException
     * @throws TranslationInvalidArgumentException
     * @throws APIRepositoryInvalidArgumentException
     * @throws UnauthorizedException
     * @throws InvalidOptionsException
     */
    public function updateMainLocationAction(Request $request): Response
    {
        $form = $this->formFactory->updateContentMainLocation();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (ContentMainLocationUpdateData $data) {
                $contentInfo = $data->getContentInfo();

                $contentMetadataUpdateStruct = $this->contentMainLocationUpdateMapper->reverseMap($data);

                $this->contentService->updateContentMetadata($contentInfo, $contentMetadataUpdateStruct);

                $this->notificationHandler->success(
                    /** @Desc("Main Location for '%name%' updated.") */
                    'content.main_location_update.success',
                    ['%name%' => $contentInfo->name],
                    'content'
                );

                return new RedirectResponse($this->generateUrl('_ez_content_view', [
                    'contentId' => $contentInfo->id,
                    'locationId' => $contentInfo->mainLocationId,
                    '_fragment' => 'ez-tab-location-view-locations',
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        /** @var ContentEditData $data */
        $data = $form->getData();
        $contentInfo = $data->getContentInfo();

        if (null !== $contentInfo) {
            return new RedirectResponse($this->generateUrl('_ez_content_view', [
                'contentId' => $contentInfo->id,
                'locationId' => $contentInfo->mainLocationId,
                '_fragment' => 'ez-tab-location-view-locations',
            ]));
        }

        return $this->redirectToRoute('ezplatform.dashboard');
    }

    /**
     * @param Content $content
     * @param string|null $languageCode
     * @param int|null $versionNo
     * @param Location|null $location
     *
     * @return Response
     */
    public function previewAction(
        Content $content,
        ?string $languageCode = null,
        ?int $versionNo = null,
        ?Location $location = null
    ): Response {
        if (null === $languageCode) {
            $languageCode = $content->contentInfo->mainLanguageCode;
        }

        // nonpublished content should use parent location instead because location doesn't exist yet
        if (!$content->contentInfo->published && null === $content->contentInfo->mainLocationId) {
            $versionInfo = $this->contentService->loadVersionInfo($content->contentInfo, $versionNo);
            $parentLocations = $this->locationService->loadParentLocationsForDraftContent($versionInfo);
            $location = reset($parentLocations);
            $versionNo = null;
        }

        if (null === $location) {
            $location = $this->locationService->loadLocation($content->contentInfo->mainLocationId);
        }

        $siteaccesses = $this->siteaccessResolver->getSiteaccessesForLocation($location, $versionNo, $languageCode);

        if (empty($siteaccesses)) {
            throw new BadStateException(
                'siteaccess',
                'There is no SiteAccess available for this Content item'
            );
        }

        return $this->render('@ezdesign/content/content_preview.html.twig', [
            'location' => $location,
            'content' => $content,
            'language_code' => $languageCode,
            'siteaccesses' => $siteaccesses,
            'version_no' => $versionNo ?? $content->getVersionInfo()->versionNo,
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateMainTranslationAction(Request $request): Response
    {
        $form = $this->createForm(MainTranslationUpdateType::class, new MainTranslationUpdateData());
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (MainTranslationUpdateData $data) {
                $content = $data->getContent();
                $contentInfo = $content->contentInfo;
                $mapper = new MainTranslationUpdateMapper();
                $contentMetadataUpdateStruct = $mapper->reverseMap($data);
                $this->contentService->updateContentMetadata($contentInfo, $contentMetadataUpdateStruct);
                $this->notificationHandler->success(
                    /** @Desc("Main language for '%name%' updated.") */
                    'content.main_language_update.success',
                    ['%name%' => $this->translationHelper->getTranslatedContentName($content)],
                    'content'
                );

                return new RedirectResponse($this->generateUrl('_ez_content_view', [
                    'contentId' => $contentInfo->id,
                    'locationId' => $contentInfo->mainLocationId,
                    '_fragment' => 'ez-tab-location-view-translations',
                ]));
            });
            if ($result instanceof Response) {
                return $result;
            }
        }
        /** @var \EzSystems\EzPlatformAdminUi\Form\Data\Content\Translation\MainTranslationUpdateData $data */
        $data = $form->getData();
        $contentInfo = $data->getContentInfo();
        if (null !== $contentInfo) {
            return new RedirectResponse($this->generateUrl('_ez_content_view', [
                'contentId' => $contentInfo->id,
                'locationId' => $contentInfo->mainLocationId,
                '_fragment' => 'ez-tab-location-view-translations',
            ]));
        }

        return $this->redirectToRoute('ezplatform.dashboard');
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateVisibilityAction(Request $request): Response
    {
        $form = $this->createForm(ContentVisibilityUpdateType::class);
        $form->handleRequest($request);
        $result = null;

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (ContentVisibilityUpdateData $data) {
                $contentInfo = $data->getContentInfo();
                $contentName = $this->translationHelper->getTranslatedContentNameByContentInfo($contentInfo);
                $desiredVisibility = $data->getVisible();
                $location = $data->getLocation();

                if ($contentInfo->isHidden && $desiredVisibility === false) {
                    $this->notificationHandler->success(
                        /** @Desc("Content item '%name%' is already hidden.") */
                        'content.hide.already_hidden',
                        ['%name%' => $contentName],
                        'content'
                    );
                }

                if (!$contentInfo->isHidden && $desiredVisibility === true) {
                    $this->notificationHandler->success(
                        /** @Desc("Content item '%name%' is already visible.") */
                        'content.reveal.already_visible',
                        ['%name%' => $contentName],
                        'content'
                    );
                }

                if (!$contentInfo->isHidden && $desiredVisibility === false) {
                    $this->contentService->hideContent($contentInfo);

                    $this->notificationHandler->success(
                        /** @Desc("Content item '%name%' hidden.") */
                        'content.hide.success',
                        ['%name%' => $contentName],
                        'content'
                    );
                }

                if ($contentInfo->isHidden && $desiredVisibility === true) {
                    $this->contentService->revealContent($contentInfo);

                    $this->notificationHandler->success(
                        /** @Desc("Content item '%name%' revealed.") */
                        'content.reveal.success',
                        ['%name%' => $contentName],
                        'content'
                    );
                }

                return $location === null ? $this->redirectToRoute('ezplatform.dashboard') : $this->redirectToLocation($location);
            });
        }

        return $result instanceof Response ? $result : $this->redirectToRoute('ezplatform.dashboard');
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     * @param string|null $languageCode
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function checkEditPermissionAction(Content $content, ?string $languageCode): JsonResponse
    {
        $targets = [];

        if (null !== $languageCode) {
            $targets[] = (new Target\Builder\VersionBuilder())->translateToAnyLanguageOf([$languageCode])->build();
        }

        $canEdit = $this->permissionResolver->canUser(
            'content',
            'edit',
            $content,
            $targets
        );

        $lookupLimitations = $this->permissionResolver->lookupLimitations(
            'content',
            'edit',
            $content,
            $targets,
            [Limitation::LANGUAGE]
        );

        $editLanguagesLimitationValues = $this->lookupLimitationsTransformer->getFlattenedLimitationsValues($lookupLimitations);

        $response = new JsonResponse();
        $response->setData([
            'canEdit' => $canEdit,
            'editLanguagesLimitationValues' => $canEdit ? $editLanguagesLimitationValues : [],
        ]);

        // Disable HTTP cache
        $response->setPrivate();
        $response->setMaxAge(0);
        $response->setSharedMaxAge(0);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->headers->addCacheControlDirective('no-store', true);

        return $response;
    }

    public function relationViewAction(int $contentId): Response
    {
        try {
            $content = $this->contentService->loadContent($contentId);
        } catch (UnauthorizedException $exception) {
            return $this->render('@ezdesign/content/relation_unauthorized.html.twig', [
                'contentId' => $contentId,
            ]);
        }

        return $this->render('@ezdesign/content/relation.html.twig', [
            'content' => $content,
            'contentType' => $content->getContentType(),
        ]);
    }
}
