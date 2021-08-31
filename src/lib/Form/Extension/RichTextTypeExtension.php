<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Extension;

use EzSystems\EzPlatformRichText\Form\Type\RichTextType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RichTextTypeExtension extends AbstractTypeExtension
{
    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'udw_context' => [
                'language' => null,
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars += [
            'udw_context' => $options['udw_context'],
        ];
    }

    public static function getExtendedTypes(): iterable
    {
        return [RichTextType::class];
    }
}
