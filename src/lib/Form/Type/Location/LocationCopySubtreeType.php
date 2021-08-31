<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Location;

use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationCopySubtreeData;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationCopySubtreeType extends AbstractLocationCopyType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LocationCopySubtreeData::class,
            'translation_domain' => 'forms',
        ]);
    }
}
