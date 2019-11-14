<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\FormMapper;

use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\User\UserGroup;
use EzSystems\EzPlatformContentForms\Data\Content\FieldData;
use EzSystems\EzPlatformContentForms\Data\User\UserCreateData;
use Symfony\Component\OptionsResolver\Exception\AccessException;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\Exception\NoSuchOptionException;
use Symfony\Component\OptionsResolver\Exception\OptionDefinitionException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form data mapper for user creation.
 */
class UserCreateMapper
{
    /**
     * @param ContentType $contentType
     * @param UserGroup[] $parentGroups
     * @param array $params
     *
     * @return UserCreateData
     *
     * @throws UndefinedOptionsException
     * @throws OptionDefinitionException
     * @throws NoSuchOptionException
     * @throws MissingOptionsException
     * @throws InvalidOptionsException
     * @throws AccessException
     */
    public function mapToFormData(ContentType $contentType, array $parentGroups, array $params = []): UserCreateData
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $params = $resolver->resolve($params);

        $data = new UserCreateData(['contentType' => $contentType, 'mainLanguageCode' => $params['mainLanguageCode']]);
        $data->setParentGroups($parentGroups);

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

    private function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setRequired('mainLanguageCode');
    }
}
