<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\Section;

use EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionContentAssignData;
use EzSystems\EzPlatformAdminUi\Form\Type\Embedded\SectionType;
use EzSystems\EzPlatformAdminUi\Form\Type\UniversalDiscoveryWidget\UniversalDiscoveryWidgetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SectionContentAssignType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'section',
                SectionType::class,
                [
                    'label' => false,
                    'multiple' => false,
                ]
            )
            ->add(
                'locations',
                UniversalDiscoveryWidgetType::class,
                [
                    'label' => /** @Desc("Assign content") */
                        'section_content_assign_form.locations',
                    'multiple' => true,
                    'title' => 'section.assign.content',
                ]
            )
            ->add(
                'assign',
                SubmitType::class,
                [
                    'label' => /** @Desc("Assign content") */
                        'section_content_assign_form.assign',
                    'attr' => ['hidden' => true],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SectionContentAssignData::class,
            'translation_domain' => 'forms',
        ]);
    }
}
