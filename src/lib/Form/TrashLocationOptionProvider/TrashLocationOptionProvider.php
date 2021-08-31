<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\TrashLocationOptionProvider;

use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Component\Form\FormInterface;

interface TrashLocationOptionProvider
{
    public function supports(Location $location): bool;

    public function addOptions(FormInterface $form, Location $location): void;
}
