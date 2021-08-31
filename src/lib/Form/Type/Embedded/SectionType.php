<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Embedded;

use eZ\Publish\API\Repository\SectionService;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\SectionsTransformer;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\SectionTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SectionType extends AbstractType
{
    /** @var \eZ\Publish\API\Repository\SectionService */
    protected $sectionService;

    /**
     * @param \eZ\Publish\API\Repository\SectionService $sectionService
     */
    public function __construct(SectionService $sectionService)
    {
        $this->sectionService = $sectionService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer($options['multiple']
            ? new SectionsTransformer($this->sectionService)
            : new SectionTransformer($this->sectionService)
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
