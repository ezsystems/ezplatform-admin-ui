<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer\FieldType;

use eZ\Publish\Core\FieldType\Country\Value;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * DataTransformer for Country\Value to be used with form type handling only single selection.
 * Needed to display the form field correctly and transform it back to an appropriate value object.
 */
class SingleCountryValueTransformer implements DataTransformerInterface
{
    /**
     * @var array Array of countries from ezpublish.fieldType.ezcountry.data
     */
    protected $countriesInfo;

    /**
     * @param array $countriesInfo
     */
    public function __construct(array $countriesInfo)
    {
        $this->countriesInfo = $countriesInfo;
    }

    public function transform($value)
    {
        if (!$value instanceof Value) {
            return null;
        }

        return current($value->countries)['Alpha2'];
    }

    public function reverseTransform($value)
    {
        if (empty($value)) {
            return null;
        }

        return new Value([$value => $this->countriesInfo[$value]]);
    }
}
