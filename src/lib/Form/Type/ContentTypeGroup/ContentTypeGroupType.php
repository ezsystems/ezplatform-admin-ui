<?php

declare(strict_types=1);

namespace EzPlatformAdminUi\Form\Type\ContentTypeGroup;

use EzPlatformAdminUi\Form\Data\ContentTypeGroupData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentTypeGroupType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('identifier', TextType::class, [
            'label' => 'content_type.group.identifier'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContentTypeGroupData::class,
            'translation_domain' => 'ezrepoforms_content_type'
        ]);
    }
}
