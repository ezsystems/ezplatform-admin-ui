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
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException as APIRepositoryUnauthorizedException;
use EzSystems\EzPlatformAdminUi\UI\Action\EventDispatcherInterface;
use EzSystems\EzPlatformAdminUi\UI\Action\FormUiActionMappingDispatcher;
use EzSystems\EzPlatformAdminUi\UI\Action\UiActionEventInterface;

class BookmarkController extends Controller
{
    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    private $formFactory;

    /** @var \EzSystems\EzPlatformAdminUi\Form\SubmitHandler */
    private $submitHandler;

    /** @var \eZ\Publish\API\Repository\BookmarkService */
    private $bookmarkService;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Action\EventDispatcherInterface */
    private $uiActionEventDispatcher;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Action\FormUiActionMappingDispatcher */
    private $formUiActionMappingDispatcher;

    public function __construct(
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        BookmarkService $bookmarkService,
        EventDispatcherInterface $uiActionEventDispatcher,
        FormUiActionMappingDispatcher $formUiActionMappingDispatcher
    ) {
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->bookmarkService = $bookmarkService;
        $this->uiActionEventDispatcher = $uiActionEventDispatcher;
        $this->formUiActionMappingDispatcher = $formUiActionMappingDispatcher;
    }

    public function updateBookmarkAction(Request $request): JsonResponse
    {
        $updateBookmarkForm = $this->formFactory->updateBookmarkLocation();
        $updateBookmarkForm->handleRequest($request);

        /** @var \EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationUpdateBookmarkData $data */
        $data = $updateBookmarkForm->getData();
        $location = $data->getLocation();

        if ($updateBookmarkForm->isValid()) {
            $data = $updateBookmarkForm->getData();
            try {
                if ($data->isBookmarked()) {
                    $this->bookmarkService->createBookmark($location);
                } else {
                    $this->bookmarkService->deleteBookmark($location);
                }
                $response = new JsonResponse();
                $event = $this->formUiActionMappingDispatcher->dispatch($updateBookmarkForm);
                $event->setResponse($response);
                $event->setType(UiActionEventInterface::TYPE_SUCCESS);

                $this->uiActionEventDispatcher->dispatch($event);

                return $response;
            } catch (APIRepositoryUnauthorizedException $e) {
                return new JsonResponse(['errors' => [$e->getMessage()]], Response::HTTP_UNAUTHORIZED);
            } catch (InvalidArgumentException $e) {
                return new JsonResponse(['errors' => [$e->getMessage()]], Response::HTTP_UNPROCESSABLE_ENTITY);
            } catch (NotFoundException $e) {
                return new JsonResponse(['errors' => [$e->getMessage()]], Response::HTTP_NOT_FOUND);
            }
        } else {
            $errors = [];
            foreach ($updateBookmarkForm->getErrors(true, true) as $formError) {
                $errors[] = $formError->getMessage();
            }

            return new JsonResponse(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
