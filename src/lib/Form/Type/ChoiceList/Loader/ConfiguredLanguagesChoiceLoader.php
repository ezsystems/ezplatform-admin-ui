<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader;

use eZ\Publish\API\Repository\LanguageService;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

class ConfiguredLanguagesChoiceLoader implements ChoiceLoaderInterface
{
    /** @var \eZ\Publish\API\Repository\LanguageService */
    protected $languageService;

    /** @var string[] */
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
     * {@inheritdoc}
     */
    public function getChoiceList(): array
    {
        return $this->getPriorityOrderedLanguages();
    }

    /**
     * {@inheritdoc}
     */
    public function loadChoiceList($value = null)
    {
        $choices = $this->getChoiceList();

        return new ArrayChoiceList($choices, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function loadChoicesForValues(array $values, $value = null)
    {
        // Optimize
        $values = array_filter($values);
        if (empty($values)) {
            return [];
        }

        return $this->loadChoiceList($value)->getChoicesForValues($values);
    }

    /**
     * {@inheritdoc}
     */
    public function loadValuesForChoices(array $choices, $value = null)
    {
        // Optimize
        $choices = array_filter($choices);
        if (empty($choices)) {
            return [];
        }

        return $this->loadChoiceList($value)->getValuesForChoices($choices);
    }

    /**
     * Sort languages based on siteaccess languages order
     *
     * @return array
     */
    private function getPriorityOrderedLanguages(): array
    {
        $languages = $this->languageService->loadLanguages();
        $languagesAssoc = [];

        foreach ($languages as $language) {
            $languagesAssoc[$language->languageCode] = $language;
        }

        $orderedLanguages = [];

        foreach ($this->siteAccessLanguages as $saLanguageCode) {
            if (isset($languagesAssoc[$saLanguageCode])) {
                $orderedLanguages[] = $languagesAssoc[$saLanguageCode];
            }
        }

        return $orderedLanguages;
    }
}
