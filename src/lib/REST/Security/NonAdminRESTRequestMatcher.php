<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\REST\Security;

use Ibexa\AdminUi\Specification\SiteAccess\IsAdmin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

final class NonAdminRESTRequestMatcher implements RequestMatcherInterface
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

class_alias(NonAdminRESTRequestMatcher::class, 'EzSystems\EzPlatformAdminUi\REST\Security\NonAdminRESTRequestMatcher');
