<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\FormMapper;

use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentTranslationData;
use EzSystems\EzPlatformContentForms\Data\Content\FieldData;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentTranslationMapper implements FormDataMapperInterface
{
    /**
     * Maps a ValueObject from eZ content repository to a data usable as underlying form data (e.g. create/update
     * struct).
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Content|\eZ\Publish\API\Repository\Values\ValueObject $content
     * @param array $params
     *
     * @return \EzSystems\EzPlatformAdminUi\Form\Data\ContentTranslationData
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\OptionDefinitionException
     * @throws \Symfony\Component\OptionsResolver\Exception\NoSuchOptionException
     * @throws \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function mapToFormData(ValueObject $content, array $params = [])
    {
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);
        $params = $optionsResolver->resolve($params);

        /** @var \eZ\Publish\API\Repository\Values\Content\Language $language */
        $language = $params['language'];

        /** @var \eZ\Publish\API\Repository\Values\Content\Language|null $baseLanguage */
        $baseLanguage = $params['baseLanguage'];
        $baseLanguageCode = $baseLanguage ? $baseLanguage->languageCode : null;

        /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType */
        $contentType = $params['contentType'];

        $data = new ContentTranslationData(['content' => $content, 'contentType' => $contentType]);
        $data->initialLanguageCode = $language->languageCode;

        foreach ($content->getFieldsByLanguage() as $field) {
            $fieldDef = $contentType->getFieldDefinition($field->fieldDefIdentifier);
            $fieldValue = null !== $baseLanguageCode
                ? $content->getFieldValue($fieldDef->identifier, $baseLanguageCode)
                : $fieldDef->defaultValue;
            $data->addFieldData(new FieldData([
                'fieldDefinition' => $fieldDef,
                'field' => $field,
                'value' => $fieldDef->isTranslatable
                    ? $fieldValue
                    : $field->value,
            ]));
        }

        return $data;
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
