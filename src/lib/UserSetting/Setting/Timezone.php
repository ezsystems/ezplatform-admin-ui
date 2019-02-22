<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UserSetting\Setting;

use EzSystems\EzPlatformUser\UserSetting\FormMapperInterface;
use EzSystems\EzPlatformUser\UserSetting\Setting\Timezone as BaseTimezone;
use EzSystems\EzPlatformUser\UserSetting\ValueDefinitionInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @deprecated Deprecated since 1.5, to be removed in 2.0. Use \EzSystems\EzPlatformUser\UserSetting\Setting\Timezone instead.
 */
class Timezone implements ValueDefinitionInterface, FormMapperInterface
{
    /** @var \EzSystems\EzPlatformUser\UserSetting\Setting\Timezone */
    private $timezone;

    /**
     * @param \EzSystems\EzPlatformUser\UserSetting\Setting\Timezone $timezone
     */
    public function __construct(BaseTimezone $timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->timezone->getName();
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->timezone->getDescription();
    }

    /**
     * @param string $storageValue
     *
     * @return string
     */
    public function getDisplayValue(string $storageValue): string
    {
        return $this->timezone->getDisplayValue($storageValue);
    }

    /**
     * @return string
     */
    public function getDefaultValue(): string
    {
        return $this->timezone->getDefaultValue();
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $formBuilder
     * @param \EzSystems\EzPlatformUser\UserSetting\ValueDefinitionInterface $value
     *
     * @return \Symfony\Component\Form\FormBuilderInterface
     */
    public function mapFieldForm(
        FormBuilderInterface $formBuilder,
        \EzSystems\EzPlatformAdminUi\UserSetting\ValueDefinitionInterface $value
    ): FormBuilderInterface {
        return $this->timezone->mapFieldForm($formBuilder, $value);
    }
}
