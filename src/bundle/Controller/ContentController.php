<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions as ApiException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\Base\Exceptions\BadStateException;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException as AdminInvalidArgumentException;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentEditData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Location\ContentMainLocationUpdateData;
use EzSystems\EzPlatformAdminUi\Form\DataMapper\ContentMainLocationUpdateMapper;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use EzSystems\EzPlatformAdminUi\Siteaccess\SiteaccessResolverInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\Translation\Exception\InvalidArgumentException;
use Symfony\Component\Translation\TranslatorInterface;
use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException as APIRepositoryInvalidArgumentException;
use Symfony\Component\Translation\Exception\InvalidArgumentException as TranslationInvalidArgumentException;

class ContentController extends Controller
{
    /** @var NotificationHandlerInterface */
    private $notificationHandler;

    /** @var ContentService */
    private $contentService;

    /** @var FormFactory */
    private $formFactory;

    /** @var SubmitHandler */
    private $submitHandler;

    /** @var TranslatorInterface */
    private $translator;

    /** @var ContentMainLocationUpdateMapper */
    private $contentMainLocationUpdateMapper;

    /** @var SiteaccessResolverInterface */
    private $siteaccessResolver;

    /** @var LocationService */
    private $locationService;

    /**
     * @param NotificationHandlerInterface $notificationHandler
     * @param ContentService $contentService
     * @param FormFactory $formFactory
     * @param SubmitHandler $submitHandler
     * @param TranslatorInterface $translator
     * @param ContentMainLocationUpdateMapper $contentMetadataUpdateMapper
     * @param SiteaccessResolverInterface $siteaccessResolver
     * @param LocationService $locationService
     */
    public function __construct(
        NotificationHandlerInterface $notificationHandler,
        ContentService $contentService,
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        TranslatorInterface $translator,
        ContentMainLocationUpdateMapper $contentMetadataUpdateMapper,
        SiteaccessResolverInterface $siteaccessResolver,
        LocationService $locationService
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->contentService = $contentService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->translator = $translator;
        $this->contentMainLocationUpdateMapper = $contentMetadataUpdateMapper;
        $this->siteaccessResolver = $siteaccessResolver;
        $this->locationService = $locationService;
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

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->submitHandler->handle($form, function (ContentCreateData $data) {
                $contentType = $data->getContentType();
                $language = $data->getLanguage();
                $parentLocation = $data->getParentLocation();

                return $this->redirectToRoute('ez_content_create_no_draft', [
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

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->submitHandler->handle($form, function (ContentEditData $data) {
                $contentInfo = $data->getContentInfo();
                $versionInfo = $data->getVersionInfo();
                $language = $data->getLanguage();
                $versionNo = $versionInfo->versionNo;
                $location = $data->getLocation();

                if (!$versionInfo->isDraft()) {
                    $contentDraft = $this->contentService->createContentDraft($contentInfo, $versionInfo);
                    $versionNo = $contentDraft->getVersionInfo()->versionNo;

                    $this->notificationHandler->success(
                        $this->translator->trans(
                            /** @Desc("New Version Draft for '%name%' created.") */
                            'content.create_draft.success',
                            ['%name%' => $contentInfo->name],
                            'content'
                        )
                    );
                }

                return $this->redirectToRoute('ez_content_draft_edit', [
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
            return $this->redirectToRoute('_ezpublishLocation', [
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

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->submitHandler->handle($form, function (ContentMainLocationUpdateData $data) {
                $contentInfo = $data->getContentInfo();

                $contentMetadataUpdateStruct = $this->contentMainLocationUpdateMapper->reverseMap($data);

                $this->contentService->updateContentMetadata($contentInfo, $contentMetadataUpdateStruct);

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Main location for '%name%' updated.") */
                        'content.main_location_update.success',
                        ['%name%' => $contentInfo->name],
                        'content'
                    )
                );

                return new RedirectResponse($this->generateUrl('_ezpublishLocation', [
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
            return new RedirectResponse($this->generateUrl('_ezpublishLocation', [
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
                'There is no siteaccesses available for particular content'
            );
        }

        return $this->render('@ezdesign/content/content_preview.html.twig', [
            'location' => $location,
            'content' => $content,
            'language_code' => $languageCode,
            'siteaccesses' => $siteaccesses,
            'versionNo' => $versionNo ?? $content->getVersionInfo()->versionNo,
        ]);
    }
}
