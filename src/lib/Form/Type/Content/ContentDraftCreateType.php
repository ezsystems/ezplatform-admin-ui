<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\Content;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ContentDraftCreateType extends AbstractType
{
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezrepoforms_content_draft_create';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'contentId',
                TextType::class,
                [
                    'label' => 'Content Id',
                    'required' => true,
                ]
            )
            ->add(
                'fromVersionNo',
                TextType::class,
                [
                    'label' => 'From version',
                    'required' => false,
                ]
            )
            ->add(
                'fromLanguage',
                TextType::class,
                [
                    'label' => 'From language',
                    'required' => false,
                ]
            )
            ->add(
                'toLanguage',
                TextType::class,
                [
                    'label' => 'To language',
                    'required' => false,
                ]
            )
            ->add(
                'createDraft',
                SubmitType::class,
                ['label' => 'Create and edit draft']
            );
    }
}
