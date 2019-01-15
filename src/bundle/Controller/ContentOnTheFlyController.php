<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions as ApiException;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\ContentCreateStruct;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Base\Exceptions\UnauthorizedException;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Dispatcher\ContentOnTheFlyDispatcher;
use EzSystems\EzPlatformAdminUi\RepositoryForms\View\ContentCreateOnTheFlyView;
use EzSystems\RepositoryForms\Data\Mapper\ContentCreateMapper;
use EzSystems\RepositoryForms\Form\Type\Content\ContentEditType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ContentOnTheFlyController extends Controller
{
    /** @var ContentService */
    private $contentService;

    /** @var LanguageService */
    private $languageService;

    /** @var LocationService */
    private $locationService;

    /** @var ContentOnTheFlyDispatcher */
    private $contentActionDispatcher;

    /**
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\LanguageService $languageService
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \EzSystems\EzPlatformAdminUi\RepositoryForms\Dispatcher\ContentOnTheFlyDispatcher $contentActionDispatcher
     */
    public function __construct(
        ContentService $contentService,
        LanguageService $languageService,
        LocationService $locationService,
        ContentOnTheFlyDispatcher $contentActionDispatcher
    ) {
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->languageService = $languageService;
        $this->contentActionDispatcher = $contentActionDispatcher;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $languageCode
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType
     * @param \eZ\Publish\API\Repository\Values\Content\Location $parentLocation
     *
     * @return \EzSystems\EzPlatformAdminUi\RepositoryForms\View\ContentCreateOnTheFlyView|null|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    public function createContentAction(
        Request $request,
        string $languageCode,
        ContentType $contentType,
        Location $parentLocation
    ) {
        $language = $this->languageService->loadLanguage($languageCode);

        $data = (new ContentCreateMapper())->mapToFormData($contentType, [
            'mainLanguageCode' => $language->languageCode,
            'parentLocation' => $this->locationService->newLocationCreateStruct($parentLocation->id),
        ]);

        $form = $this->createForm(ContentEditType::class, $data, [
            'languageCode' => $language->languageCode,
            'mainLanguageCode' => $language->languageCode,
            'drafts_enabled' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->getClickedButton()) {
            $this->contentActionDispatcher->dispatchFormAction($form, $data, $form->getClickedButton()->getName());
            if ($response = $this->contentActionDispatcher->getResponse()) {
                return $response;
            }
        }

        return new ContentCreateOnTheFlyView('@ezdesign/content/content_on_the_fly/content_create_on_the_fly.html.twig', [
            'form' => $form->createView(),
            'language' => $language,
            'contentType' => $contentType,
            'parentLocation' => $parentLocation,
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $languageCode
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType
     * @param \eZ\Publish\API\Repository\Values\Content\Location $parentLocation
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function hasCreateAccessAction(
        Request $request,
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
}
