<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UserSetting;

use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use EzSystems\EzPlatformUser\UserSetting\FormMapperInterface;
use EzSystems\EzPlatformUser\UserSetting\ValueDefinitionInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Autosave implements ValueDefinitionInterface, FormMapperInterface
{
    public const ENABLED_OPTION = 'enabled';
    public const DISABLED_OPTION = 'disabled';

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
    }

    public function getName(): string
    {
        return $this->getTranslatedName();
    }

    public function getDescription(): string
    {
        return $this->getTranslatedDescription();
    }

    public function getDisplayValue(string $storageValue): string
    {
        switch ($storageValue) {
            case self::ENABLED_OPTION:
                return $this->getTranslatedOptionEnabled();
            case self::DISABLED_OPTION:
                return $this->getTranslatedOptionDisabled();
            default:
                throw new InvalidArgumentException(
                    '$storageValue',
                    sprintf('There is no \'%s\' option', $storageValue)
                );
        }
    }

    public function getDefaultValue(): string
    {
        return self::ENABLED_OPTION;
    }

    public function mapFieldForm(
        FormBuilderInterface $formBuilder,
        ValueDefinitionInterface $value
    ): FormBuilderInterface {
        $choices = [
            $this->getTranslatedOptionEnabled() => self::ENABLED_OPTION,
            $this->getTranslatedOptionDisabled() => self::DISABLED_OPTION,
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

    private function getTranslatedName(): string
    {
        return $this->translator->trans(
            /** @Desc("Autosave draft") */
            'settings.autosave.value.title',
            [],
            'user_settings'
        );
    }

    private function getTranslatedDescription(): string
    {
        return $this->translator->trans(
            /** @Desc("Autosave draft every given period") */
            'settings.autosave.value.description',
            [],
            'user_settings'
        );
    }

    private function getTranslatedOptionEnabled(): string
    {
        return $this->translator->trans(
            /** @Desc("enabled") */
            'settings.autosave.value.enabled',
            [],
            'user_settings'
        );
    }

    private function getTranslatedOptionDisabled(): string
    {
        return $this->translator->trans(
            /** @Desc("disabled") */
            'settings.autosave.value.disabled',
            [],
            'user_settings'
        );
    }
}
