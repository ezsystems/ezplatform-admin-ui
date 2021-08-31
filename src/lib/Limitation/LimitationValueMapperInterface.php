<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Limitation;

use eZ\Publish\API\Repository\Values\User\Limitation;

/**
 * Interface for Limitation Value mappers.
 */
interface LimitationValueMapperInterface
{
    /**
     * Map the limitation values, in order to pass them as context of limitation value rendering.
     *
     * @param \eZ\Publish\API\Repository\Values\User\Limitation $limitation
     *
     * @return mixed[]
     */
    public function mapLimitationValue(Limitation $limitation);
}

class_alias(
    LimitationValueMapperInterface::class,
    \EzSystems\RepositoryForms\Limitation\LimitationValueMapperInterface::class
);
