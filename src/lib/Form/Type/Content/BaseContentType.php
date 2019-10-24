<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\Content;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Base Type used on User or Content create/edit forms.
 */
class BaseContentType extends AbstractType
{
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezrepoforms_content';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fieldsData', CollectionType::class, [
                'entry_type' => ContentFieldType::class,
                'label' => 'ezrepoforms.content.fields',
                'entry_options' => [
                    'languageCode' => $options['languageCode'],
                    'mainLanguageCode' => $options['mainLanguageCode'],
                ],
            ])
            ->add('redirectUrlAfterPublish', HiddenType::class, [
                'required' => false,
                'mapped' => false,
            ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['languageCode'] = $options['languageCode'];
        $view->vars['mainLanguageCode'] = $options['mainLanguageCode'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['languageCode', 'mainLanguageCode']);
    }
}
