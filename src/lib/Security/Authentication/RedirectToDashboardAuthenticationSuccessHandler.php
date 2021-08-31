<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Security\Authentication;

use eZ\Publish\Core\MVC\Symfony\Security\Authentication\DefaultAuthenticationSuccessHandler;
use EzSystems\EzPlatformAdminUi\Specification\SiteAccess\IsAdmin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class RedirectToDashboardAuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{
    use TargetPathTrait;

    /** @var array */
    private $siteAccessGroups;

    /** @var string */
    private $defaultTargetPath;

    /**
     * @param \Symfony\Component\Security\Http\HttpUtils $httpUtils
     * @param array $options
     * @param array $siteAccessGroups
     * @param string $defaultTargetPath
     */
    public function __construct(
        HttpUtils $httpUtils,
        array $options = [],
        array $siteAccessGroups = [],
        string $defaultTargetPath
    ) {
        parent::__construct($httpUtils, $options);
        $this->siteAccessGroups = $siteAccessGroups;
        $this->defaultTargetPath = $defaultTargetPath;
    }

    /**
     * Builds the target URL according to the defined options.
     * Overwrites default page after login for admin siteaccess.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return string
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     */
    protected function determineTargetUrl(Request $request)
    {
        if ((new IsAdmin($this->siteAccessGroups))->isSatisfiedBy($request->attributes->get('siteaccess'))) {
            $this->options['default_target_path'] = $this->defaultTargetPath;
            $target = $this->getTargetPath($request->getSession(), $this->providerKey);
            if (null !== $target && 1 === count(explode('/', trim(parse_url($target)['path'], '/')))) {
                $this->options['always_use_default_target_path'] = true;
            }
        }

        return parent::determineTargetUrl($request);
    }
}
