<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Action;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Util\StringUtil;

/**
 * Generic Form to Ui Action mapper.
 */
class FormUiActionMapper implements FormUiActionMapperInterface
{
    public function map(FormInterface $form): UiActionEvent
    {
        $data = $form->getData();
        $name = is_array($data)
            ? $form->getName()
            : StringUtil::fqcnToBlockPrefix(get_class($data));

        return new UiActionEvent($name, UiActionEventInterface::TYPE_SUCCESS, $form, null);
    }

    public function supports(FormInterface $form): bool
    {
        return true;
    }
}
