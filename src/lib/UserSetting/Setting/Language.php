<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UserSetting\Setting;

use eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface;
use EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader\AvailableLocaleChoiceLoader;
use EzSystems\EzPlatformAdminUi\UserSetting\FormMapperInterface;
use EzSystems\EzPlatformAdminUi\UserSetting\ValueDefinitionInterface;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class Language implements ValueDefinitionInterface, FormMapperInterface
{
    /** @var \Symfony\Component\Translation\TranslatorInterface */
    private $translator;

    /** @var \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface */
    private $userLanguagePreferenceProvider;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader\AvailableLocaleChoiceLoader */
    private $availableLocaleChoiceLoader;

    /**
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface $userLanguagePreferenceProvider
     * @param \EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader\AvailableLocaleChoiceLoader $availableLocaleChoiceLoader
     */
    public function __construct(
        TranslatorInterface $translator,
        UserLanguagePreferenceProviderInterface $userLanguagePreferenceProvider,
        AvailableLocaleChoiceLoader $availableLocaleChoiceLoader
    ) {
        $this->translator = $translator;
        $this->userLanguagePreferenceProvider = $userLanguagePreferenceProvider;
        $this->availableLocaleChoiceLoader = $availableLocaleChoiceLoader;
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
        $preferredLocales = $this->userLanguagePreferenceProvider->getPreferredLocales();

        return reset($preferredLocales);
    }

    /**
     * {@inheritdoc}
     */
    public function mapFieldForm(FormBuilderInterface $formBuilder, ValueDefinitionInterface $value): FormBuilderInterface
    {
        return $formBuilder->create(
            'value',
            LocaleType::class,
            [
                'required' => true,
                'label' => $this->getTranslatedDescription(),
                'choice_loader' => $this->availableLocaleChoiceLoader,
            ]
        );
    }

    /**
     * @return string
     */
    private function getTranslatedName(): string
    {
        return $this->translator->trans(
            /** @Desc("Language") */
            'settings.language.value.title',
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
            /** @Desc("The language of the administration panel") */
            'settings.language.value.description',
            [],
            'user_settings'
        );
    }
}
