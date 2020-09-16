<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\REST\Security;

use EzSystems\EzPlatformAdminUi\Specification\SiteAccess\IsAdmin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

final class AdminRESTRequestMatcher implements RequestMatcherInterface
{
    /** @var string[][] */
    private $siteAccessGroups;

    public function __construct(array $siteAccessGroups)
    {
        $this->siteAccessGroups = $siteAccessGroups;
    }

    public function matches(Request $request): bool
    {
        return
            $request->attributes->get('is_rest_request') &&
            !$this->isAdminSiteAccess($request);
    }

    private function isAdminSiteAccess(Request $request): bool
    {
        return (new IsAdmin($this->siteAccessGroups))->isSatisfiedBy($request->attributes->get('siteaccess'));
    }
}
