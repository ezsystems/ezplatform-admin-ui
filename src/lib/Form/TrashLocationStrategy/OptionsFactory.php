<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\TrashLocationStrategy;

use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Component\Form\FormInterface;

final class OptionsFactory
{
    /** @var \EzSystems\EzPlatformAdminUi\Form\TrashLocationStrategy\TrashLocationOptionProvider[] */
    private $strategies;

    public function __construct(iterable $strategies)
    {
        $this->strategies = $strategies;
    }

    public function addOptions(FormInterface $form, ?Location $location = null)
    {
        if (!$location) {
            return;
        }

        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($location)) {
                $strategy->addOptions($form, $location);
            }
        }
    }
}
