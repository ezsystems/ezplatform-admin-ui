<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Provider;

interface ChoiceListProviderInterface
{
    public function getChoiceList(): array;
}
