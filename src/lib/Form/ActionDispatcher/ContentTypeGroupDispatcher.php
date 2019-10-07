<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\ActionDispatcher;

use EzSystems\EzPlatformAdminUi\Event\RepositoryFormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentTypeGroupDispatcher extends AbstractActionDispatcher
{
    protected function configureOptions(OptionsResolver $resolver)
    {
        // Nothing to do
    }

    /**
     * Returns base for action event name. It will be used as default action event name.
     * By convention, other action event names will have the format "<actionEventBaseName>.<actionName>".
     *
     * @return string
     */
    protected function getActionEventBaseName()
    {
        return RepositoryFormEvents::CONTENT_TYPE_GROUP_UPDATE;
    }
}
