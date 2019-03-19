<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Siteaccess;

use eZ\Publish\Core\MVC\ConfigResolverInterface;

abstract class AbstractSiteaccessPreviewVoter implements SiteaccessPreviewVoterInterface
{
    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    protected $configResolver;

    /**
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     */
    public function __construct(
        ConfigResolverInterface $configResolver
    ) {
        $this->configResolver = $configResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function vote(SiteaccessPreviewVoterContext $context): bool
    {
        $siteaccess = $context->getSiteaccess();
        $location = $context->getLocation();
        $languageCode = $context->getLanguageCode();
        $contentLanguages = $context->getVersionInfo()->languageCodes;

        if (empty(array_intersect($this->getRootLocationIds($siteaccess), $location->path))) {
            return false;
        }

        $siteaccessLanguages = $this->configResolver->getParameter(
            'languages',
            null,
            $siteaccess
        );
        if (!in_array($languageCode, $siteaccessLanguages)) {
            return false;
        }

        $primarySiteaccessLanguage = reset($siteaccessLanguages);
        if (
            $languageCode !== $primarySiteaccessLanguage
            && in_array($primarySiteaccessLanguage, $contentLanguages)
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param string $siteaccess
     *
     * @return int[]
     */
    abstract protected function getRootLocationIds(string $siteaccess): array;
}
