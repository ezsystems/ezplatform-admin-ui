<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader;

use eZ\Publish\API\Repository\Values\Content\Language;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

class ContentCreateLanguageChoiceLoader implements ChoiceLoaderInterface
{
    /** @var \EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader\LanguageChoiceLoader */
    private $languageChoiceLoader;

    /** @var string[] */
    private $restrictedLanguagesCodes;

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader\LanguageChoiceLoader $languageChoiceLoader
     * @param array $restrictedLanguagesCodes
     */
    public function __construct(
        LanguageChoiceLoader $languageChoiceLoader,
        array $restrictedLanguagesCodes
    ) {
        $this->languageChoiceLoader = $languageChoiceLoader;
        $this->restrictedLanguagesCodes = $restrictedLanguagesCodes;
    }

    /**
     * @inheritdoc
     */
    public function loadChoiceList($value = null)
    {
        $languages = $this->languageChoiceLoader->getChoiceList();

        if (empty($this->restrictedLanguagesCodes)) {
            return new ArrayChoiceList($languages, $value);
        }

        $languages = array_filter($languages, function (Language $language) {
            return \in_array($language->languageCode, $this->restrictedLanguagesCodes, true);
        });

        return new ArrayChoiceList($languages, $value);
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function loadValuesForChoices(array $choices, $value = null)
    {
        // Optimize
        $choices = array_filter($choices);
        if (empty($choices)) {
            return [];
        }

        // If no callable is set, choices are the same as values
        if (null === $value) {
            return $choices;
        }

        return $this->loadChoiceList($value)->getValuesForChoices($choices);
    }
}
