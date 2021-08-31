<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use Symfony\Component\Form\DataTransformerInterface;

class MultilingualSelectionTransformer implements DataTransformerInterface
{
    /** @var string */
    protected $languageCode;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData */
    private $data;

    /**
     * @param string $languageCode
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData $data
     */
    public function __construct(string $languageCode, FieldDefinitionData $data)
    {
        $this->languageCode = $languageCode;
        $this->data = $data;
    }

    /**
     * @inheritdoc
     */
    public function transform($value)
    {
        return $value;
    }

    /**
     * @inheritdoc
     */
    public function reverseTransform($value)
    {
        if (!$value) {
            return [$this->languageCode => []];
        }

        return array_merge($this->data->fieldSettings['multilingualOptions'], [$this->languageCode => $value]);
    }
}
