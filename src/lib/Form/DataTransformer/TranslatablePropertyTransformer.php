<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Data transformer to deal with translatable properties, where values are indexed by language code.
 */
class TranslatablePropertyTransformer implements DataTransformerInterface
{
    /**
     * Current language code (e.g. eng-GB).
     *
     * @var string
     */
    private $languageCode;

    public function __construct($languageCode)
    {
        $this->languageCode = $languageCode;
    }

    public function transform($valueAsHash)
    {
        if (!($valueAsHash && is_array($valueAsHash) && isset($valueAsHash[$this->languageCode]))) {
            return null;
        }

        return $valueAsHash[$this->languageCode];
    }

    public function reverseTransform($value)
    {
        if (!$value) {
            return null;
        }

        return [$this->languageCode => $value];
    }
}
