<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UserSetting;

use Symfony\Component\Form\FormBuilderInterface;

interface FormMapperInterface
{
    /**
     * Creates 'value' form type representing editing control for setting user preference value.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $formBuilder
     * @param \EzSystems\EzPlatformAdminUi\UserSetting\ValueDefinitionInterface $value
     *
     * @return \Symfony\Component\Form\FormBuilderInterface
     */
    public function mapFieldForm(
        FormBuilderInterface $formBuilder,
        ValueDefinitionInterface $value
    ): FormBuilderInterface;
}
