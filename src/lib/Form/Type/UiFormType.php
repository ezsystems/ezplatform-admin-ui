<?php
declare(strict_types=1);

namespace EzPlatformAdminUi\Form\Type;


use EzPlatformAdminUi\Form\Data\UiFormData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UiFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('data', null, ['label' => false])
            ->add('on_success_redirection_url', HiddenType::class)
            ->add('on_failure_redirection_url', HiddenType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', UiFormData::class);
    }
}
