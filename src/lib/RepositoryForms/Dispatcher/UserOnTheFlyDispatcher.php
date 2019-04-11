<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Dispatcher;

use EzSystems\RepositoryForms\Form\ActionDispatcher\AbstractActionDispatcher;

class UserOnTheFlyDispatcher extends AbstractActionDispatcher
{
    const EVENT_BASE_NAME = 'user.on_the_fly';

    /**
     * Configures options to pass to the form action event.
     * Might do nothing if there are no options.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    protected function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
    }

    /**
     * Returns base for action event name. It will be used as default action event name.
     * By convention, other action event names will have the format "<actionEventBaseName>.<actionName>".
     *
     * @return string
     */
    protected function getActionEventBaseName()
    {
        return self::EVENT_BASE_NAME;
    }
}
