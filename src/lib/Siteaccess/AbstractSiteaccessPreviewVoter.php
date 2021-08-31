<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Siteaccess;

use eZ\Bundle\EzPublishCoreBundle\ApiLoader\RepositoryConfigurationProvider;
use eZ\Publish\Core\MVC\ConfigResolverInterface;

abstract class AbstractSiteaccessPreviewVoter implements SiteaccessPreviewVoterInterface
{
    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    protected $configResolver;

    /** @var \eZ\Bundle\EzPublishCoreBundle\ApiLoader\RepositoryConfigurationProvider */
    protected $repositoryConfigurationProvider;

    public function __construct(
        ConfigResolverInterface $configResolver,
        RepositoryConfigurationProvider $repositoryConfigurationProvider
    ) {
        $this->configResolver = $configResolver;
        $this->repositoryConfigurationProvider = $repositoryConfigurationProvider;
    }

    /**
     * @inheritdoc
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

        if (!$this->validateRepositoryMatch($siteaccess)) {
            return false;
        }

        $siteaccessLanguages = $this->configResolver->getParameter(
            'languages',
            null,
            $siteaccess
        );

        if (!in_array($languageCode, $siteaccessLanguages, true)) {
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

    protected function validateRepositoryMatch(string $siteaccess): bool
    {
        $siteaccessRepository = $this->configResolver->getParameter(
            'repository',
            null,
            $siteaccess
        );

        // If SA does not have a repository configured we should obtain the default one, which is used as a fallback.
        $siteaccessRepository = $siteaccessRepository ?: $this->repositoryConfigurationProvider->getDefaultRepositoryAlias();
        $currentRepository = $this->repositoryConfigurationProvider->getCurrentRepositoryAlias();

        return $siteaccessRepository === $currentRepository;
    }

    /**
     * @param string $siteaccess
     *
     * @return int[]
     */
    abstract protected function getRootLocationIds(string $siteaccess): array;
}
