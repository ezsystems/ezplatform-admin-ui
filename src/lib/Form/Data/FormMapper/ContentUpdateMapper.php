<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\FormMapper;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\EzPlatformContentForms\Data\Content\ContentUpdateData;
use EzSystems\EzPlatformContentForms\Data\Content\FieldData;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentUpdateMapper implements FormDataMapperInterface
{
    /**
     * Maps a ValueObject from eZ content repository to a data usable as underlying form data (e.g. create/update struct).
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Content|\eZ\Publish\API\Repository\Values\ValueObject $contentDraft
     * @param array $params
     *
     * @return \EzSystems\EzPlatformContentForms\Data\Content\ContentUpdateData
     */
    public function mapToFormData(ValueObject $contentDraft, array $params = [])
    {
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);

        $params = $optionsResolver->resolve($params);
        $languageCode = $params['languageCode'];

        $data = new ContentUpdateData(['contentDraft' => $contentDraft]);
        $data->initialLanguageCode = $languageCode;

        $fields = $contentDraft->getFieldsByLanguage($languageCode);
        foreach ($params['contentType']->fieldDefinitions as $fieldDef) {
            $field = $fields[$fieldDef->identifier];
            $data->addFieldData(new FieldData([
                'fieldDefinition' => $fieldDef,
                'field' => $field,
                'value' => $field->value,
            ]));
        }

        return $data;
    }

    private function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver
            ->setRequired(['languageCode', 'contentType'])
            ->setAllowedTypes('contentType', ContentType::class);
    }
}
