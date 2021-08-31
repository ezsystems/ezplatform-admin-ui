<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\FieldDefinition\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordConstraintCheckboxType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new CallbackTransformer('boolval', 'boolval'));
    }

    /**
     * @inheritdoc
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['label'] = 'field_definition.ezuser.' . $this->toSnakeCase($view->vars['name']);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'translation_domain' => 'content_type',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getParent(): string
    {
        return CheckboxType::class;
    }

    /**
     * Converts given $string to the snake case.
     *
     * @param string $string
     *
     * @return string
     */
    private function toSnakeCase(string $string): string
    {
        return strtolower(preg_replace('/[A-Z]/', '_\\0', lcfirst($string)));
    }
}
