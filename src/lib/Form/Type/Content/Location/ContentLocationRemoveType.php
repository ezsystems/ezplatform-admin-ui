<?php
declare(strict_types=1);

namespace EzPlatformAdminUi\Form\Type\Content\Location;


use EzPlatformAdminUi\Form\Data\Content\Location\ContentLocationRemoveData;
use EzPlatformAdminUi\Form\Type\Content\ContentInfoType;
use Symfony\Component\Form\{
    AbstractType, FormBuilderInterface
};
use Symfony\Component\Form\Extension\Core\Type\{
    CheckboxType, CollectionType, SubmitType
};
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentLocationRemoveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'content_info',
                ContentInfoType::class,
                ['label' => false]
            )
            ->add(
                'locations',
                CollectionType::class,
                [
                    'entry_type' => CheckboxType::class,
                    'required' => false,
                    'allow_add' => true,
                    'entry_options' => ['label' => false],
                    'label' => false,
                ]
            )
            ->add(
                'remove',
                SubmitType::class,
                [
                    'attr' => ['hidden' => true],
                    'label' => /** @Desc("Delete") */
                        'content_location_remove_form.remove',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContentLocationRemoveData::class,
            'translation_domain' => 'forms',
        ]);
    }
}
