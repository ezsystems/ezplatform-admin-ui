<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller\Location;

use eZ\Publish\API\Repository\BookmarkService;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationUpdateBookmarkData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    public function updateBookmarkAction(Request $request): Response
    {
        $updateBookmarkForm = $this->formFactory->updateBookmarkLocation();
        $updateBookmarkForm->handleRequest($request);

        /** @var \EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationUpdateBookmarkData $data */
        $data = $updateBookmarkForm->getData();
        $location = $data->getLocation();

        if ($updateBookmarkForm->isValid()) {
            $result = $this->submitHandler->handle($updateBookmarkForm, function (LocationUpdateBookmarkData $data) use ($location) {
                if ($data->isBookmarked()) {
                    $this->bookmarkService->createBookmark($location);
                } else {
                    $this->bookmarkService->deleteBookmark($location);
                }

                return $this->redirectToLocation($location);
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        if ($location) {
            return $this->redirectToLocation($location);
        }

        return $this->redirectToRoute('ezplatform.dashboard');
    }
}
