<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use EzSystems\EzSupportToolsBundle\SystemInfo\SystemInfoCollectorRegistry;
use Symfony\Component\HttpFoundation\Response;

class SystemInfoController extends Controller
{
    /** @var SystemInfoCollectorRegistry */
    protected $collectorRegistry;

    /**
     * @param SystemInfoCollectorRegistry $collectorRegistry
     */
    public function __construct(SystemInfoCollectorRegistry $collectorRegistry)
    {
        $this->collectorRegistry = $collectorRegistry;
    }

    /**
     * Renders the system information page.
     *
     * @return Response
     */
    public function infoAction(): Response
    {
        return $this->render('@ezdesign/admin/systeminfo/info.html.twig', [
            'collector_identifiers' => $this->collectorRegistry->getIdentifiers(),
        ]);
    }

    /**
     * Renders a PHP info page.
     *
     * @return Response
     */
    public function phpinfoAction(): Response
    {
        ob_start();
        phpinfo();
        $response = new Response(ob_get_clean());

        return $response;
    }
}
