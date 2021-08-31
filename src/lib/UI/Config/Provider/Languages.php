<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Config\Provider;

use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformAdminUi\UI\Config\ProviderInterface;

/**
 * Provides information about languages.
 */
class Languages implements ProviderInterface
{
    /** @var \eZ\Publish\API\Repository\LanguageService */
    private $languageService;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var string[] */
    private $siteAccesses;

    /**
     * @param \eZ\Publish\API\Repository\LanguageService $languageService
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     * @param string[]
     */
    public function __construct(
        LanguageService $languageService,
        ConfigResolverInterface $configResolver,
        array $siteAccesses
    ) {
        $this->languageService = $languageService;
        $this->configResolver = $configResolver;
        $this->siteAccesses = $siteAccesses;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'mappings' => $languagesMap = $this->getLanguagesMap(),
            'priority' => $this->getLanguagesPriority($languagesMap),
        ];
    }

    /**
     * @return array
     */
    protected function getLanguagesMap(): array
    {
        $languagesMap = [];

        foreach ($this->languageService->loadLanguages() as $language) {
            $languagesMap[$language->languageCode] = [
                'name' => $language->name,
                'id' => $language->id,
                'languageCode' => $language->languageCode,
                'enabled' => $language->enabled,
            ];
        }

        return $languagesMap;
    }

    /**
     * Returns list of languages in a prioritized form.
     *
     * First: languages that are main of siteaccesses are displayed first.
     * Next: fallback languages of siteaccesses.
     * Last: languages defined but not used in siteaccesses.
     *
     * @param array $languagesMap data from call to getLanguagesMap()
     *
     * @return array
     */
    protected function getLanguagesPriority(array $languagesMap): array
    {
        $priority = [];
        $fallback = [];

        foreach ($this->siteAccesses as $siteAccess) {
            $siteAccessLanguages = $this->configResolver->getParameter('languages', null, $siteAccess);
            $priority[] = array_shift($siteAccessLanguages);
            $fallback = array_merge($fallback, $siteAccessLanguages);
        }

        // Append fallback languages at the end of priority language list
        $languageCodes = array_unique(array_merge($priority, $fallback));

        $languages = array_filter(array_values($languageCodes), static function ($languageCode) use ($languagesMap) {
            // Get only Languages defined and enabled in Admin
            return isset($languagesMap[$languageCode]) && $languagesMap[$languageCode]['enabled'];
        });

        // Languages that are not configured in any of SiteAccess but user is still able to create content
        $unused = array_diff(array_keys($languagesMap), $languages);

        return array_merge($languages, $unused);
    }
}
