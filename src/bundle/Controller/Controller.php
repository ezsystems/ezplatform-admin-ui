<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class Controller extends AbstractController
{
    public function performAccessCheck()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param string $uriFragment
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToLocation(Location $location, string $uriFragment = ''): RedirectResponse
    {
        return $this->redirectToRoute('_ez_content_view', [
            'contentId' => $location->contentId,
            'locationId' => $location->id,
            '_fragment' => $uriFragment,
        ]);
    }
}
