<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Extension\Content;

use EzSystems\RepositoryForms\Form\Type\Content\ContentEditType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Extends Content Edit form with additional fields.
 */
class ContentEditTypeExtension extends AbstractTypeExtension
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(/** @Desc("Preview") */'preview', SubmitType::class, [
            'label' => 'preview',
            'attr' => ['hidden' => true],
            'translation_domain' => 'content_preview',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType(): string
    {
        return ContentEditType::class;
    }
}
