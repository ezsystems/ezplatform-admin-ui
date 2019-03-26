<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Permission;

use eZ\Publish\API\Repository\Values\User\LookupLimitationResult;

/**
 * @internal
 */
final class LookupLimitationsTransformer
{
    /**
     * @param \eZ\Publish\API\Repository\Values\User\LookupLimitationResult $lookupLimitations
     *
     * @return array
     */
    public function getFlattenedLimitationsValues(LookupLimitationResult $lookupLimitations): array
    {
        $limitationsValues = [];

        /** @var \eZ\Publish\API\Repository\Values\User\LookupPolicyLimitations $lookupPolicyLimitation */
        foreach ($lookupLimitations->lookupPolicyLimitations as $lookupPolicyLimitation) {
            /** @var \eZ\Publish\API\Repository\Values\User\Limitation $limitation */
            foreach ($lookupPolicyLimitation->limitations as $limitation) {
                $limitationsValues[] = $limitation->limitationValues;
            }
        }

        return !empty($limitationsValues) ? array_unique(array_merge(...$limitationsValues)) : $limitationsValues;
    }
}
