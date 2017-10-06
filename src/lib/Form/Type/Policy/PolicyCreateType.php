<?php
declare(strict_types=1);

namespace EzPlatformAdminUi\Form\Type\Policy;

use EzPlatformAdminUi\Form\Data\Policy\PolicyCreateData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PolicyCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'policy',
                PolicyChoiceType::class, [
                    'label' => /** @Desc("Type") */ 'role.policy.type',
                    'placeholder' => 'role.policy.type.choose',
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                ['label' => /** @Desc("Create") */ 'policy_create.save']
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PolicyCreateData::class,
            'translation_domain' => 'forms',
        ]);
    }
}
