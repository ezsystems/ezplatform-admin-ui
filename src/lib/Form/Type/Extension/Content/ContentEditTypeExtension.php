<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Extension\Content;

use EzSystems\EzPlatformContentForms\Form\Type\Content\ContentEditType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;

/**
 * Extends Content Edit form with additional fields.
 */
class ContentEditTypeExtension extends AbstractTypeExtension
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('preview', SubmitType::class, [
            'label' => /** @Desc("Preview") */ 'preview',
            'attr' => [
                'hidden' => true,
                'formnovalidate' => 'formnovalidate',
            ],
            'translation_domain' => 'content_preview',
        ]);

        $builder->addEventListener(FormEvents::POST_SUBMIT, static function (PostSubmitEvent $event): void {
            $form = $event->getForm();

            if ($form->get('preview')->isClicked()) {
                $event->stopPropagation();
            }
        }, 900);
    }

    public static function getExtendedTypes(): iterable
    {
        return [ContentEditType::class];
    }
}
