<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Mapper;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\ContentTranslationData;
use EzSystems\RepositoryForms\Data\Content\ContentUpdateData;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\Data\Mapper\FormDataMapperInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentTranslationMapper implements FormDataMapperInterface
{
    /**
     * Maps a ValueObject from eZ content repository to a data usable as underlying form data (e.g. create/update
     * struct).
     *
     * @param ValueObject|Content $content
     * @param array $params
     *
     * @return ContentUpdateData
     */
    public function mapToFormData(ValueObject $content, array $params = [])
    {
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);
        $params = $optionsResolver->resolve($params);

        /** @var Language $language */
        $language = $params['language'];

        /** @var Language $baseLanguage */
        $baseLanguage = $params['baseLanguage'];

        /** @var ContentType $contentType */
        $contentType = $params['contentType'];

        $data = new ContentTranslationData(['content' => $content]);
        $data->initialLanguageCode = $language->languageCode;

        foreach ($content->getFields() as $field) {
            $fieldDef = $contentType->getFieldDefinition($field->fieldDefIdentifier);
            $data->addFieldData(new FieldData([
                'fieldDefinition' => $fieldDef,
                'field' => $field,
                'value' => $baseLanguage === null && $fieldDef->isTranslatable
                    ? $fieldDef->defaultValue
                    : $field->value,
            ]));
        }

        return $data;
    }

    private function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver
            ->setRequired([
                'language',
                'contentType',
            ])
            ->setDefined(['baseLanguage'])
            ->setAllowedTypes('contentType', ContentType::class)
            ->setAllowedTypes('baseLanguage', ['null', Language::class])
            ->setAllowedTypes('language', Language::class);
    }
}
