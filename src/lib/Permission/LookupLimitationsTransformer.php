<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Permission;

use eZ\Publish\API\Repository\Values\User\LookupLimitationResult;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;

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

        foreach ($lookupLimitations->roleLimitations as $roleLimitation) {
            $limitationsValues[] = $roleLimitation->limitationValues;
        }

        /** @var \eZ\Publish\API\Repository\Values\User\LookupPolicyLimitations $lookupPolicyLimitation */
        foreach ($lookupLimitations->lookupPolicyLimitations as $lookupPolicyLimitation) {
            /** @var \eZ\Publish\API\Repository\Values\User\Limitation $limitation */
            foreach ($lookupPolicyLimitation->limitations as $limitation) {
                $limitationsValues[] = $limitation->limitationValues;
            }
        }

        return !empty($limitationsValues) ? array_unique(array_merge(...$limitationsValues)) : $limitationsValues;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\User\LookupLimitationResult $lookupLimitations
     * @param string[] $limitationsIdentifiers
     *
     * @return array
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     */
    public function getGroupedLimitationValues(
        LookupLimitationResult $lookupLimitations,
        array $limitationsIdentifiers
    ): array {
        if (empty($limitationsIdentifiers)) {
            throw new InvalidArgumentException('limitationsIdentifiers', 'must contain at least one Limitation identifier');
        }
        $groupedLimitationsValues = [];

        foreach ($limitationsIdentifiers as $limitationsIdentifier) {
            $groupedLimitationsValues[$limitationsIdentifier] = [];
        }

        foreach ($lookupLimitations->roleLimitations as $roleLimitation) {
            if (\in_array($roleLimitation->getIdentifier(), $limitationsIdentifiers, true)) {
                $groupedLimitationsValues[$roleLimitation->getIdentifier()][] = $roleLimitation->limitationValues;
            }
        }

        foreach ($lookupLimitations->lookupPolicyLimitations as $lookupPolicyLimitation) {
            /** @var \eZ\Publish\API\Repository\Values\User\Limitation $limitation */
            foreach ($lookupPolicyLimitation->limitations as $limitation) {
                if (\in_array($limitation->getIdentifier(), $limitationsIdentifiers, true)) {
                    $groupedLimitationsValues[$limitation->getIdentifier()][] = $limitation->limitationValues;
                }
            }
        }

        foreach ($groupedLimitationsValues as $identifier => $limitationsValues) {
            if (!empty($limitationsValues)) {
                $groupedLimitationsValues[$identifier] = array_unique(array_merge(...$limitationsValues));
            }
        }

        return $groupedLimitationsValues;
    }
}
