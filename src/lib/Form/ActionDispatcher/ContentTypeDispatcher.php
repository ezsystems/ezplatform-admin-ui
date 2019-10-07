<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\EzPlatformAdminUi\Form\ActionDispatcher;

use EzSystems\EzPlatformAdminUi\Event\RepositoryFormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentTypeDispatcher extends AbstractActionDispatcher
{
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('languageCode');
    }

    protected function getActionEventBaseName()
    {
        return RepositoryFormEvents::CONTENT_TYPE_UPDATE;
    }
}
