<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data;

use eZ\Publish\API\Repository\Values\ContentType\ContentTypeUpdateStruct;

/**
 * Base data class for ContentType update form, with FieldDefinitions data and ContentTypeDraft.
 *
 * @property \eZ\Publish\API\Repository\Values\ContentType\ContentTypeDraft $contentTypeDraft
 * @property \EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData[] $fieldDefinitionsData
 */
class ContentTypeData extends ContentTypeUpdateStruct implements NewnessCheckable
{
    /**
     * Trait which provides isNew(), and mandates getIdentifier().
     */
    use NewnessChecker;

    /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentTypeDraft */
    protected $contentTypeDraft;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData[] */
    protected $fieldDefinitionsData = [];

    /**
     * Language Code of currently edited contentTypeDraft.
     *
     * @var string|null
     */
    public $languageCode = null;

    protected function getIdentifierValue(): string
    {
        return $this->contentTypeDraft->identifier;
    }

    public function addFieldDefinitionData(FieldDefinitionData $fieldDefinitionData): void
    {
        $this->fieldDefinitionsData[] = $fieldDefinitionData;
    }

    public function replaceFieldDefinitionData(string $fieldDefinitionIdentifier, FieldDefinitionData $fieldDefinitionData): void
    {
        $currentFieldDefinition = array_filter(
            $this->fieldDefinitionsData,
            function (FieldDefinitionData $fieldDefinitionData) use ($fieldDefinitionIdentifier) {
                return $fieldDefinitionIdentifier === $fieldDefinitionData->identifier;
            }
        );

        $this->fieldDefinitionsData[key($currentFieldDefinition)] = $fieldDefinitionData;
    }

    /**
     * Sort $this->fieldDefinitionsData first by position, then by identifier.
     */
    public function sortFieldDefinitions(): void
    {
        usort(
            $this->fieldDefinitionsData,
            function ($a, $b) {
                if ($a->fieldDefinition->position === $b->fieldDefinition->position) {
                    // The identifiers can never be the same
                    return $a->fieldDefinition->identifier < $b->fieldDefinition->identifier ? -1 : 1;
                }

                return $a->fieldDefinition->position < $b->fieldDefinition->position ? -1 : 1;
            }
        );
    }
}
