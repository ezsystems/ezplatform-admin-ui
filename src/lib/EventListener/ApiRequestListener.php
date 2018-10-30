<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\EventListener;

use eZ\Bundle\EzPublishRestBundle\EventListener\RequestListener;
use Symfony\Component\HttpFoundation\Request;

class ApiRequestListener extends RequestListener
{
    // siteaccesname/api/method_name
    const ADMIN_UI_REST_PREFIX_PATTERN = '/[a-zA-Z0-9-_]+\/api\/[a-zA-Z0-9-_]+/';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return bool
     */
    protected function hasRestPrefix(Request $request)
    {
        return parent::hasRestPrefix($request)
            || preg_match(self::ADMIN_UI_REST_PREFIX_PATTERN, $request->getPathInfo());
    }
}
