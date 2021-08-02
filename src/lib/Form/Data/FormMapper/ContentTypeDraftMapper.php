<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\FormMapper;

use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\Core\Helper\FieldsGroups\FieldsGroupsList;
use EzSystems\EzPlatformAdminUi\Event\FieldDefinitionMappingEvent;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeData;
use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentTypeDraftMapper implements FormDataMapperInterface
{
    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    private $eventDispatcher;

    /** @var \eZ\Publish\Core\Helper\FieldsGroups\FieldsGroupsList */
    private $fieldsGroupsList;

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        FieldsGroupsList $fieldsGroupsList
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->fieldsGroupsList = $fieldsGroupsList;
    }

    /**
     * Maps a ValueObject from eZ content repository to a data usable as underlying form data (e.g. create/update struct).
     *
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentTypeDraft|\eZ\Publish\API\Repository\Values\ValueObject $contentTypeDraft
     * @param array $params
     *
     * @return \EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeData
     */
    public function mapToFormData(ValueObject $contentTypeDraft, array $params = [])
    {
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);
        $params = $optionsResolver->resolve($params);

        /** @var \eZ\Publish\API\Repository\Values\Content\Language $language */
        $language = $params['language'] ?? null;

        /** @var \eZ\Publish\API\Repository\Values\Content\Language|null $baseLanguage */
        $baseLanguage = $params['baseLanguage'] ?? null;

        $contentTypeData = new ContentTypeData(['contentTypeDraft' => $contentTypeDraft]);
        if (!$contentTypeData->isNew()) {
            $contentTypeData->identifier = $contentTypeDraft->identifier;
        }

        $contentTypeData->remoteId = $contentTypeDraft->remoteId;
        $contentTypeData->urlAliasSchema = $contentTypeDraft->urlAliasSchema;
        $contentTypeData->nameSchema = $contentTypeDraft->nameSchema;
        $contentTypeData->isContainer = $contentTypeDraft->isContainer;
        $contentTypeData->mainLanguageCode = $contentTypeDraft->mainLanguageCode;
        $contentTypeData->defaultSortField = $contentTypeDraft->defaultSortField;
        $contentTypeData->defaultSortOrder = $contentTypeDraft->defaultSortOrder;
        $contentTypeData->defaultAlwaysAvailable = $contentTypeDraft->defaultAlwaysAvailable;
        $contentTypeData->names = $contentTypeDraft->getNames();
        $contentTypeData->descriptions = $contentTypeDraft->getDescriptions();

        $contentTypeData->languageCode = $language ? $language->languageCode : $contentTypeDraft->mainLanguageCode;

        if ($baseLanguage && $language) {
            $contentTypeData->names[$language->languageCode] = $contentTypeDraft->getName($baseLanguage->languageCode);
            $contentTypeData->descriptions[$language->languageCode] = $contentTypeDraft->getDescription($baseLanguage->languageCode);
        }

        foreach ($contentTypeDraft->fieldDefinitions as $fieldDef) {
            $fieldDefinitionData = new FieldDefinitionData([
                'fieldDefinition' => $fieldDef,
                'contentTypeData' => $contentTypeData,
            ]);

            $event = new FieldDefinitionMappingEvent(
                $fieldDefinitionData,
                $baseLanguage,
                $language
            );

            $this->eventDispatcher->dispatch($event, FieldDefinitionMappingEvent::NAME);

            if (empty($fieldDefinitionData->fieldGroup)) {
                $fieldDefinitionData->fieldGroup = $this->fieldsGroupsList->getDefaultGroup();
            }

            $contentTypeData->addFieldDefinitionData($event->getFieldDefinitionData());
        }
        $contentTypeData->sortFieldDefinitions();

        return $contentTypeData;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    private function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver
            ->setDefined(['language'])
            ->setDefined(['baseLanguage'])
            ->setAllowedTypes('baseLanguage', ['null', Language::class])
            ->setAllowedTypes('language', Language::class);
    }
}
