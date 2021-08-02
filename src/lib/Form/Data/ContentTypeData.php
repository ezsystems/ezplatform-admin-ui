<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data;

use eZ\Publish\API\Repository\Values\ContentType\ContentTypeUpdateStruct;

/**
 * Base data class for ContentType update form, with FieldDefinitions data and ContentTypeDraft.
 *
 * @property \eZ\Publish\API\Repository\Values\ContentType\ContentTypeDraft $contentTypeDraft
 */
class ContentTypeData extends ContentTypeUpdateStruct implements NewnessCheckable
{
    /**
     * Trait which provides isNew(), and mandates getIdentifier().
     */
    use NewnessChecker;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData[][] */
    public $fieldDefinitionsData = [];

    /**
     * Language Code of currently edited contentTypeDraft.
     *
     * @var string|null
     */
    public $languageCode = null;

    /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentTypeDraft */
    protected $contentTypeDraft;

    protected function getIdentifierValue(): string
    {
        return $this->contentTypeDraft->identifier;
    }

    /**
     * @return iterable<string, \EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData>
     */
    public function getFlatFieldDefinitionsData(): iterable
    {
        foreach ($this->fieldDefinitionsData as $outerKey => $fieldDefinitionGroupData) {
            foreach ($fieldDefinitionGroupData as $innerKey => $fieldDefinitionData) {
                yield "$outerKey.$innerKey" => $fieldDefinitionData;
            }
        }
    }

    public function addFieldDefinitionData(FieldDefinitionData $fieldDefinitionData): void
    {
        $this->fieldDefinitionsData[$fieldDefinitionData->fieldGroup][$fieldDefinitionData->identifier] = $fieldDefinitionData;
    }

    public function replaceFieldDefinitionData(string $fieldDefinitionIdentifier, FieldDefinitionData $fieldDefinitionData): void
    {
        foreach ($this->fieldDefinitionsData as $key => $fieldDefinitionsByGroup) {
            if (isset($this->fieldDefinitionsData[$key][$fieldDefinitionIdentifier])) {
                unset($this->fieldDefinitionsData[$key][$fieldDefinitionIdentifier]);
            }
        }

        $this->fieldDefinitionsData[$fieldDefinitionData->fieldGroup][$fieldDefinitionIdentifier] = $fieldDefinitionData;
    }

    /**
     * Sort $this->fieldDefinitionsData first by position, then by identifier.
     */
    public function sortFieldDefinitions(): void
    {
        foreach ($this->fieldDefinitionsData as $key => $fieldDefinitionByGroup) {
            uasort(
                $fieldDefinitionByGroup,
                static function ($a, $b): int {
                    if ($a->fieldDefinition->position === $b->fieldDefinition->position) {
                        return $a->fieldDefinition->identifier <=> $b->fieldDefinition->identifier;
                    }

                    return $a->fieldDefinition->position <=> $b->fieldDefinition->position;
                }
            );

            $this->fieldDefinitionsData[$key] = $fieldDefinitionByGroup;
        }
    }
}
