<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Content;

use eZ\Publish\API\Repository\LocationService;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\LocationsTransformer;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\LocationTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationType extends AbstractType
{
    /** @var \eZ\Publish\API\Repository\LocationService */
    protected $locationService;

    /**
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     */
    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer($options['multiple']
            ? new LocationsTransformer($this->locationService)
            : new LocationTransformer($this->locationService)
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('multiple', false);
        $resolver->setRequired(['multiple']);
        $resolver->setAllowedTypes('multiple', 'boolean');
    }

    public function getParent(): ?string
    {
        return HiddenType::class;
    }
}
