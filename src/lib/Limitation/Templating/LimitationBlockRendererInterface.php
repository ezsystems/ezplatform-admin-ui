<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Limitation\Templating;

use eZ\Publish\API\Repository\Values\User\Limitation;

interface LimitationBlockRendererInterface
{
    /**
     * Returns limitation value in human readable format.
     *
     * @param \eZ\Publish\API\Repository\Values\User\Limitation $limitation
     * @param array $parameters
     *
     * @return string
     */
    public function renderLimitationValue(Limitation $limitation, array $parameters = []);
}
