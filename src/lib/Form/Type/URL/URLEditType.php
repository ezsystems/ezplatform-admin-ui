<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\URL;

use EzSystems\EzPlatformAdminUi\Form\Data\URL\URLUpdateData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * URL edit form.
 */
class URLEditType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('url', TextType::class);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => URLUpdateData::class,
            'translation_domain' => 'ezplatform_content_forms_url',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return 'ezplatform_content_forms_url_edit';
    }
}
