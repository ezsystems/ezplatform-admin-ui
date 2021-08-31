<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\Location;

use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationCopyData;
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
