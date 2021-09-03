<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\AdminUi\Form\Type\Location;

use Ibexa\AdminUi\Form\Data\Location\LocationCopyData;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationCopyType extends AbstractLocationCopyType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LocationCopyData::class,
            'translation_domain' => 'forms',
        ]);
    }
}

class_alias(LocationCopyType::class, 'EzSystems\EzPlatformAdminUi\Form\Type\Location\LocationCopyType');
