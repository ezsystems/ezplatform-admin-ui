<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UserSetting\Setting;

use EzSystems\EzPlatformUser\UserSetting\ValueDefinitionInterface;
use EzSystems\EzPlatformUser\UserSetting\FormMapperInterface;
use EzSystems\EzPlatformUser\UserSetting\Setting\SubitemsLimit as BaseSubitemsLimit;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @deprecated Deprecated since 1.5, to be removed in 2.0. Use \EzSystems\EzPlatformUser\UserSetting\Setting\SubitemsLimit instead.
 */
class SubitemsLimit implements ValueDefinitionInterface, FormMapperInterface
{
    /** @var \EzSystems\EzPlatformUser\UserSetting\Setting\SubitemsLimit */
    private $subitemsLimit;

    /**
     * @param \EzSystems\EzPlatformUser\UserSetting\Setting\SubitemsLimit $subitemsLimit
     */
    public function __construct(BaseSubitemsLimit $subitemsLimit)
    {
        $this->subitemsLimit = $subitemsLimit;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->subitemsLimit->getName();
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->subitemsLimit->getDescription();
    }

    /**
     * @param string $storageValue
     *
     * @return string
     */
    public function getDisplayValue(string $storageValue): string
    {
        return $this->subitemsLimit->getDisplayValue($storageValue);
    }

    /**
     * @return string
     */
    public function getDefaultValue(): string
    {
        return $this->subitemsLimit->getDefaultValue();
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
        return $this->subitemsLimit->mapFieldForm($formBuilder, $value);
    }
}
