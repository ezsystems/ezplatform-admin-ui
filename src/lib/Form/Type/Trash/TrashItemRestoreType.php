<?php

namespace EzPlatformAdminUi\Form\Type\Trash;

use EzPlatformAdminUi\Form\Data\Trash\TrashItemRestoreData;
use EzPlatformAdminUi\Form\Data\TrashItemData;
use EzPlatformAdminUi\Form\Type\UniversalDiscoveryWidget\UniversalDiscoveryWidgetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrashItemRestoreType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'trash_items',
                TrashItemChoiceType::class,
                [
                    'multiple' => true,
                    'expanded' => true,
                    'choice_label' => false,
                    'label' => false,
                ]
            )
            ->add(
                'location',
                UniversalDiscoveryWidgetType::class,
                [
                    'multiple' => false,
                    'label' => /** @Desc("Restore under new parent") */
                        'trash_item_restore_form.restore_under_new_parent',
                    'attr' => $options['attr'],
                ]
            )
            ->add(
                'restore',
                SubmitType::class,
                [
                    'label' => /** @Desc("Restore selected") */
                        'trash_item_restore_form.restore',
                ]
            );
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrashItemRestoreData::class,
            'translation_domain' => 'forms',
        ]);
    }
}

