<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\AdminUi\UI\Action;

use Ibexa\AdminUi\UI\Action\UiActionEvent;
use Symfony\Component\Form\FormInterface;

interface FormUiActionMapperInterface
{
    /**
     * Maps $form object to UiActionEvent object.
     *
     * @param \Symfony\Component\Form\FormInterface $form
     *
     * @return \EzSystems\EzPlatformAdminUi\UI\Action\UiActionEvent
     */
    public function map(FormInterface $form): UiActionEvent;

    /**
     * Returns true if FormUiActionMapper is able to create Event from the $form;.
     *
     * @param \Symfony\Component\Form\FormInterface $form
     *
     * @return bool
     */
    public function supports(FormInterface $form): bool;
}

class_alias(FormUiActionMapperInterface::class, 'EzSystems\EzPlatformAdminUi\UI\Action\FormUiActionMapperInterface');
