<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UserSetting;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformUser\UserSetting\FormMapperInterface;
use EzSystems\EzPlatformUser\UserSetting\ValueDefinitionInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AutosaveInterval implements ValueDefinitionInterface, FormMapperInterface
{
    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    public function __construct(
        TranslatorInterface $translator,
        ConfigResolverInterface $configResolver
    ) {
        $this->translator = $translator;
        $this->configResolver = $configResolver;
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
        return $storageValue;
    }

    public function getDefaultValue(): string
    {
        return (string)$this->configResolver->getParameter('autosave.interval');
    }

    public function mapFieldForm(
        FormBuilderInterface $formBuilder,
        ValueDefinitionInterface $value
    ): FormBuilderInterface {
        return $formBuilder->create(
            'value',
            NumberType::class,
            [
                'attr' => ['min' => 30],
                'required' => true,
                'label' => $this->getTranslatedDescription(),
            ]
        );
    }

    private function getTranslatedName(): string
    {
        return $this->translator->trans(
            /** @Desc("Autosave interval") */
            'settings.autosave_interval.value.title',
            [],
            'user_settings'
        );
    }

    private function getTranslatedDescription(): string
    {
        return $this->translator->trans(
            /** @Desc("Seconds till next draft autosave") */
            'settings.autosave_interval.value.description',
            [],
            'user_settings'
        );
    }
}
