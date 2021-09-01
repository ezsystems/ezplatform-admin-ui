<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\AdminUi\Form\ActionDispatcher;

use Ibexa\Contracts\AdminUi\Event\FormEvents;
use EzSystems\EzPlatformContentForms\Form\ActionDispatcher\AbstractActionDispatcher;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentTypeDispatcher extends AbstractActionDispatcher
{
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('languageCode');
    }

    protected function getActionEventBaseName()
    {
        return FormEvents::CONTENT_TYPE_UPDATE;
    }
}

class_alias(ContentTypeDispatcher::class, 'EzSystems\EzPlatformAdminUi\Form\ActionDispatcher\ContentTypeDispatcher');
