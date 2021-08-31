<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\FormMapper;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\User\User;
use EzSystems\EzPlatformContentForms\Data\Content\FieldData;
use EzSystems\EzPlatformContentForms\Data\User\UserUpdateData;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form data mapper for user updating.
 */
class UserUpdateMapper
{
    /**
     * Maps a ValueObject from eZ content repository to a data usable as underlying form data (e.g. create/update struct).
     *
     * @param \eZ\Publish\API\Repository\Values\User\User $user
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType
     * @param array $params
     *
     * @return \EzSystems\EzPlatformContentForms\Data\User\UserUpdateData
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\OptionDefinitionException
     * @throws \Symfony\Component\OptionsResolver\Exception\NoSuchOptionException
     * @throws \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function mapToFormData(User $user, ContentType $contentType, array $params = []): UserUpdateData
    {
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);
        $params = $optionsResolver->resolve($params);

        $data = new UserUpdateData([
            'user' => $user,
            'enabled' => $user->enabled,
            'contentType' => $contentType,
        ]);

        $fields = $user->getFieldsByLanguage($params['languageCode']);
        foreach ($contentType->fieldDefinitions as $fieldDef) {
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
            ->setRequired(['languageCode']);
    }
}
