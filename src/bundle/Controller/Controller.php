<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

abstract class Controller extends BaseController
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
        return $this->redirectToRoute('_ezpublishLocation', [
            'locationId' => $location->id,
            '_fragment' => $uriFragment,
        ]);
    }

    /**
     * Redirects to the previous URL or to the dashboard if referer is different host.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function redirectBack(Request $request): RedirectResponse
    {
        $referer = $request->headers->get('referer');
        $host = $request->getSchemeAndHttpHost();

        if (strpos($referer, $host) === 0) {
            return $this->redirect($referer);
        }

        return $this->redirect($this->generateUrl('ezplatform.dashboard'));
    }
}
