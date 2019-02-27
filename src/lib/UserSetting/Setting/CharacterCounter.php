<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UserSetting\Setting;

use EzSystems\EzPlatformAdminUi\UserSetting\FormMapperInterface;
use EzSystems\EzPlatformAdminUi\UserSetting\ValueDefinitionInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class CharactersCounter implements ValueDefinitionInterface, FormMapperInterface
{
    /** @var \Symfony\Component\Translation\TranslatorInterface */
    private $translator;

    /**
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->getTranslatedName();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): string
    {
        return $this->getTranslatedDescription();
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayValue(string $storageValue): string
    {
        return $storageValue;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultValue(): string
    {
        return 'enabled';
    }

    /**
     * {@inheritdoc}
     */
    public function mapFieldForm(FormBuilderInterface $formBuilder, ValueDefinitionInterface $value): FormBuilderInterface
    {
        $choices = [
            $this->getTranslatedOptionEnabled() => 'enabled',
            $this->getTranslatedOptionDisabled() => 'disabled',
        ];

        return $formBuilder->create(
            'value',
            ChoiceType::class,
            [
                'multiple' => false,
                'required' => true,
                'label' => $this->getTranslatedDescription(),
                'choices' => $choices,
            ]
        );
    }

    /**
     * @return string
     */
    private function getTranslatedName(): string
    {
        return $this->translator->trans(
            /** @Desc("Characters counter") */
            'settings.characters_counter.value.title',
            [],
            'user_settings'
        );
    }

    /**
     * @return string
     */
    private function getTranslatedDescription(): string
    {
        return $this->translator->trans(
            /** @Desc("Displays count of characters typed in Online Editor") */
            'settings.characters_counter.value.description',
            [],
            'user_settings'
        );
    }

    /**
     * @return string
     */
    private function getTranslatedOptionEnabled(): string
    {
        return $this->translator->trans(
            /** @Desc("enabled") */
            'settings.characters_counter.value.enabled',
            [],
            'user_settings'
        );
    }

    /**
     * @return string
     */
    private function getTranslatedOptionDisabled(): string
    {
        return $this->translator->trans(
            /** @Desc("disabled") */
            'settings.characters_counter.value.disabled',
            [],
            'user_settings'
        );
    }
}
