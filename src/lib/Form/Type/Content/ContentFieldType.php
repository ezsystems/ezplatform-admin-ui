<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\Content;

use EzSystems\EzPlatformAdminUi\FieldType\FieldTypeDefinitionFormMapperDispatcherInterface;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentFieldType extends AbstractType
{
    /**
     * @var FieldTypeDefinitionFormMapperDispatcherInterface
     */
    private $fieldTypeFormMapper;

    public function __construct(FieldTypeDefinitionFormMapperDispatcherInterface $fieldTypeFormMapper)
    {
        $this->fieldTypeFormMapper = $fieldTypeFormMapper;
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezrepoforms_content_field';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => FieldData::class,
                'translation_domain' => 'ezrepoforms_content',
            ])
            ->setRequired(['languageCode', 'mainLanguageCode']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['languageCode'] = $options['languageCode'];
        $view->vars['mainLanguageCode'] = $options['mainLanguageCode'];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $this->fieldTypeFormMapper->map($event->getForm(), $event->getData());
        });
    }
}
