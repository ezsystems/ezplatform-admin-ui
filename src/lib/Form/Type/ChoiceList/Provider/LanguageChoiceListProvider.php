<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Provider;

use eZ\Publish\API\Repository\LanguageService;

class LanguageChoiceListProvider implements ChoiceListProviderInterface
{
    /** @var \eZ\Publish\API\Repository\LanguageService */
    protected $languageService;

    /** @var array */
    protected $siteAccessLanguages;

    /**
     * @param \eZ\Publish\API\Repository\LanguageService $languageService
     * @param array $siteAccessLanguages
     */
    public function __construct(LanguageService $languageService, array $siteAccessLanguages)
    {
        $this->languageService = $languageService;
        $this->siteAccessLanguages = $siteAccessLanguages;
    }

    /**
     * @return array
     */
    public function getChoiceList(): array
    {
        $saLanguages = [];
        $languagesByCode = [];

        foreach ($this->languageService->loadLanguages() as $language) {
            if ($language->enabled) {
                $languagesByCode[$language->languageCode] = $language;
            }
        }

        foreach ($this->siteAccessLanguages as $languageCode) {
            if (!isset($languagesByCode[$languageCode])) {
                continue;
            }

            $saLanguages[] = $languagesByCode[$languageCode];
            unset($languagesByCode[$languageCode]);
        }

        return array_merge($saLanguages, array_values($languagesByCode));
    }
}
