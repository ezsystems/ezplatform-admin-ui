<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\EventListener;

use EzSystems\EzPlatformAdminUi\Event\FieldDefinitionMappingEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PopulateFieldDefinitionData implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [FieldDefinitionMappingEvent::NAME => ['populateFieldDefinition', 50]];
    }

    public function populateFieldDefinition(FieldDefinitionMappingEvent $event): void
    {
        $fieldDefinition = $event->getFieldDefinition();
        $fieldDefinitionData = $event->getFieldDefinitionData();

        $fieldDefinitionData->identifier = $fieldDefinition->identifier;
        $fieldDefinitionData->names = $fieldDefinition->getNames();
        $fieldDefinitionData->descriptions = $fieldDefinition->getDescriptions();
        $fieldDefinitionData->fieldGroup = $fieldDefinition->fieldGroup;
        $fieldDefinitionData->position = $fieldDefinition->position;
        $fieldDefinitionData->isTranslatable = $fieldDefinition->isTranslatable;
        $fieldDefinitionData->isRequired = $fieldDefinition->isRequired;
        $fieldDefinitionData->isThumbnail = $fieldDefinition->isThumbnail;
        $fieldDefinitionData->isInfoCollector = $fieldDefinition->isInfoCollector;
        $fieldDefinitionData->validatorConfiguration = $fieldDefinition->getValidatorConfiguration();
        $fieldDefinitionData->fieldSettings = $fieldDefinition->getFieldSettings();
        $fieldDefinitionData->defaultValue = $fieldDefinition->defaultValue;
        $fieldDefinitionData->isSearchable = $fieldDefinition->isSearchable;

        $event->setFieldDefinitionData($fieldDefinitionData);
    }
}
