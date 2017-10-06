<?php

declare(strict_types=1);

namespace EzPlatformAdminUi\Form\Type\Content;

use eZ\Publish\API\Repository\LocationService;
use EzPlatformAdminUi\Form\DataTransformer\LocationsTransformer;
use EzPlatformAdminUi\Form\DataTransformer\LocationTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationType extends AbstractType
{
    /** @var LocationService */
    protected $locationService;

    /**
     * @param LocationService $locationService
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

    public function getParent()
    {
        return HiddenType::class;
    }
}
