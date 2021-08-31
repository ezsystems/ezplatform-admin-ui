<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Event;

use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use Symfony\Contracts\EventDispatcher\Event;

class FieldDefinitionMappingEvent extends Event
{
    /**
     * Triggered when contentTypeData is created from contentTypeDraft.
     */
    public const NAME = 'field_definition.mapping';

    /** @var \EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData */
    private $fieldDefinitionData;

    /** @var \eZ\Publish\API\Repository\Values\Content\Language|null */
    private $baseLanguage;

    /** @var \eZ\Publish\API\Repository\Values\Content\Language|null */
    private $targetLanguage;

    public function __construct(
        FieldDefinitionData $fieldDefinitionData,
        ?Language $baseLanguage,
        ?Language $targetLanguage
    ) {
        $this->baseLanguage = $baseLanguage;
        $this->targetLanguage = $targetLanguage;
        $this->fieldDefinitionData = $fieldDefinitionData;
    }

    public function getFieldDefinition(): FieldDefinition
    {
        return $this->fieldDefinitionData->fieldDefinition;
    }

    public function getFieldDefinitionData(): FieldDefinitionData
    {
        return $this->fieldDefinitionData;
    }

    public function setFieldDefinitionData(FieldDefinitionData $fieldDefinitionData): void
    {
        $this->fieldDefinitionData = $fieldDefinitionData;
    }

    public function getBaseLanguage(): ?Language
    {
        return $this->baseLanguage;
    }

    public function getTargetLanguage(): ?Language
    {
        return $this->targetLanguage;
    }
}
