<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller\Location;

use eZ\Publish\API\Repository\BookmarkService;
use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationUpdateBookmarkData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException as APIRepositoryUnauthorizedException;

class BookmarkController extends Controller
{
    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    private $formFactory;

    /** @var \EzSystems\EzPlatformAdminUi\Form\SubmitHandler */
    private $submitHandler;

    /** @var \eZ\Publish\API\Repository\BookmarkService */
    private $bookmarkService;

    public function __construct(
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        BookmarkService $bookmarkService
    ) {
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->bookmarkService = $bookmarkService;
    }

    public function updateBookmarkAction(Request $request): JsonResponse
    {
        $updateBookmarkForm = $this->formFactory->updateBookmarkLocation();
        $updateBookmarkForm->handleRequest($request);

        if ($updateBookmarkForm->isSubmitted()) {
            $result = $this->submitHandler->handleAjax($updateBookmarkForm, function (LocationUpdateBookmarkData $data) {
                $location = $data->getLocation();

                try {
                    if ($data->isBookmarked()) {
                        $this->bookmarkService->createBookmark($location);
                    } else {
                        $this->bookmarkService->deleteBookmark($location);
                    }

                    return new JsonResponse();
                } catch (APIRepositoryUnauthorizedException $e) {
                    return new JsonResponse(['errors' => [$e->getMessage()]], Response::HTTP_UNAUTHORIZED);
                } catch (InvalidArgumentException $e) {
                    return new JsonResponse(['errors' => [$e->getMessage()]], Response::HTTP_UNPROCESSABLE_ENTITY);
                } catch (NotFoundException $e) {
                    return new JsonResponse(['errors' => [$e->getMessage()]], Response::HTTP_NOT_FOUND);
                }
            });

            if ($result instanceof JsonResponse) {
                return $result;
            }
        }

        return new JsonResponse([], Response::HTTP_BAD_REQUEST);
    }
}
