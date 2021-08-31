<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\FormMapper;

use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\EzPlatformContentForms\Data\Content\ContentCreateData;
use EzSystems\EzPlatformContentForms\Data\Content\FieldData;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form data mapper for content create without a draft.
 */
class ContentCreateMapper implements FormDataMapperInterface
{
    /**
     * Maps a ValueObject from eZ content repository to a data usable as underlying form data (e.g. create/update struct).
     *
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType|\eZ\Publish\API\Repository\Values\ValueObject $contentType
     * @param array $params
     *
     * @return \EzSystems\EzPlatformContentForms\Data\Content\ContentCreateData
     */
    public function mapToFormData(ValueObject $contentType, array $params = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $params = $resolver->resolve($params);

        $data = new ContentCreateData(['contentType' => $contentType, 'mainLanguageCode' => $params['mainLanguageCode']]);
        $data->addLocationStruct($params['parentLocation']);
        foreach ($contentType->fieldDefinitions as $fieldDef) {
            $data->addFieldData(new FieldData([
                'fieldDefinition' => $fieldDef,
                'field' => new Field([
                    'fieldDefIdentifier' => $fieldDef->identifier,
                    'languageCode' => $params['mainLanguageCode'],
                ]),
                'value' => $fieldDef->defaultValue,
            ]));
        }

        return $data;
    }

    private function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver
            ->setRequired(['mainLanguageCode', 'parentLocation'])
            ->setAllowedTypes('parentLocation', '\eZ\Publish\API\Repository\Values\Content\LocationCreateStruct');
    }
}
