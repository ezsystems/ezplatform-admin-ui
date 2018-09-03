<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\User\Setting;

use EzSystems\EzPlatformAdminUi\Form\Data\User\Setting\UserSettingUpdateData;
use EzSystems\EzPlatformAdminUi\UserSetting\FormMapperRegistry;
use EzSystems\EzPlatformAdminUi\UserSetting\ValueDefinitionRegistry;
use RuntimeException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserSettingUpdateType extends AbstractType
{
    /** @var \EzSystems\EzPlatformAdminUi\UserSetting\FormMapperRegistry */
    protected $formMapperRegistry;

    /** @var \EzSystems\EzPlatformAdminUi\UserSetting\ValueDefinitionRegistry */
    protected $valueDefinitionRegistry;

    /**
     * @param \EzSystems\EzPlatformAdminUi\UserSetting\FormMapperRegistry $formMapperRegistry
     * @param \EzSystems\EzPlatformAdminUi\UserSetting\ValueDefinitionRegistry $valueDefinitionRegistry
     */
    public function __construct(
        FormMapperRegistry $formMapperRegistry,
        ValueDefinitionRegistry $valueDefinitionRegistry
    ) {
        $this->formMapperRegistry = $formMapperRegistry;
        $this->valueDefinitionRegistry = $valueDefinitionRegistry;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $formMapper = $this->formMapperRegistry->getFormMapper($options['user_setting_identifier']);
        $valueDefinition = $this->valueDefinitionRegistry->getValueDefinition($options['user_setting_identifier']);

        $builder
            ->add('identifier', HiddenType::class, [])
            ->add($formMapper->mapFieldForm($builder, $valueDefinition))
            ->add('update', SubmitType::class, [])
        ;

        if (!$builder->has('value')) {
            throw new RuntimeException("FormMapper should create 'value' field");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('user_setting_identifier')
            ->setAllowedTypes('user_setting_identifier', 'string')
            ->setAllowedValues('user_setting_identifier', array_keys($this->formMapperRegistry->getFormMappers()))
            ->setDefaults([
                'data_class' => UserSettingUpdateData::class,
                'translation_domain' => 'forms',
            ])
        ;
    }
}
