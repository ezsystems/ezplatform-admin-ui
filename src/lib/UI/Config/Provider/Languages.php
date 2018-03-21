<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Config\Provider;

use eZ\Publish\API\Repository\LanguageService;
use EzSystems\EzPlatformAdminUi\UI\Config\ProviderInterface;

/**
 * Provides information about languages.
 */
class Languages implements ProviderInterface
{
    /** @var LanguageService */
    private $languageService;

    /**
     * @param LanguageService $languageService
     */
    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'map' => $this->getLanguagesMap(),
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
}
