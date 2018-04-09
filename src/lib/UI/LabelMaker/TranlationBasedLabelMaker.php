<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\UI\LabelMaker;

use Symfony\Component\Translation\TranslatorInterface;

/**
 * Makes human readable labels using translations.
 * The item and label identifier are prefixed with a configured string, and translated in a configured domain.
 *
 * Example: $labelMaker->getLabel($myCustomTag, 'description').
 *
 * If no translation exists, uses the
 */
class TranlationBasedLabelMaker implements LabelMaker
{
    /** @var TranslatorInterface */
    private $translator;

    /** @var string[] */
    private $patterns;

    /** @var string */
    private $domain;

    public function __construct(TranslatorInterface $translator, array $config = [])
    {
        $this->translator = $translator;
        $this->patterns = $config['patterns'];
        $this->domain = $config['domain'];
    }

    public function getLabel(string $identifier, $items, bool $generateDefault = true): string
    {
        if (!is_array($items)) {
            $items = [$items];
        }

        $key = $this->makeTranslationKey($items, $identifier);
        $translation = $this->translator->trans($key, [], $this->domain);
        if ($translation !== $key) {
            return $translation;
        }

        if ($generateDefault === false) {
            return '';
        }

        $defaultItem = array_pop($items);

        return ucfirst(str_replace('_', ' ', $defaultItem));
    }

    /**
     * @param array $items
     * @param string $labelIdentifier
     *
     * @return string
     */
    private function makeTranslationKey(array $items, string $labelIdentifier): string
    {
        if (!isset($this->patterns[$labelIdentifier])) {
            throw new \InvalidArgumentException(sprintf("No pattern set for '%s'", $labelIdentifier));
        }

        return vsprintf($this->patterns[$labelIdentifier], array_merge([$labelIdentifier], $items));
    }
}
