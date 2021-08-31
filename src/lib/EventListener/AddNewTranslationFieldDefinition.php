<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\EventListener;

use EzSystems\EzPlatformAdminUi\Event\FieldDefinitionMappingEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddNewTranslationFieldDefinition implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [FieldDefinitionMappingEvent::NAME => ['addNewTranslation', 40]];
    }

    public function addNewTranslation(FieldDefinitionMappingEvent $event): void
    {
        $baseLanguage = $event->getBaseLanguage();
        $targetLanguage = $event->getTargetLanguage();

        if (null === $baseLanguage || null === $targetLanguage) {
            return;
        }

        $fieldDefinitionData = $event->getFieldDefinitionData();
        $fieldDefinition = $event->getFieldDefinition();

        $fieldDefinitionData->names[$targetLanguage->languageCode] = $fieldDefinition->getName($baseLanguage->languageCode);
        $fieldDefinitionData->descriptions[$targetLanguage->languageCode] = $fieldDefinition->getDescription($baseLanguage->languageCode);

        $event->setFieldDefinitionData($fieldDefinitionData);
    }
}
