<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Util;

use eZ\Publish\API\Repository\Values\User\Limitation\LanguageLimitation;

class PermissionUtil
{
    /**
     * @param array $limitations
     *
     * @return string[]
     */
    public function getLimitationLanguageCodes(array $limitations): array
    {
        $languages = [];

        foreach ($limitations as $limitation) {
            /** @var \eZ\Publish\Core\Repository\Values\User\Policy $policy */
            foreach ($limitation['policies'] as $policy) {
                foreach ($policy->getLimitations() as $limitationObject) {
                    if ($limitationObject instanceof LanguageLimitation) {
                        $languages[] = $limitationObject->limitationValues;
                    }
                }
            }
        }

        return !empty($languages) ? array_unique(array_merge(...$languages)) : $languages;
    }

    /**
     * This method should only be used for very specific use cases. It should be used in a content cases
     * where assignment limitations are not relevant.
     *
     * @param array $hasAccess
     *
     * @return array
     */
    public function flattenArrayOfLimitations(array $hasAccess): array
    {
        $limitations = [];
        foreach ($hasAccess as $permissionSet) {
            if ($permissionSet['limitation'] !== null) {
                $limitations[] = $permissionSet['limitation'];
            }
            /** @var \eZ\Publish\API\Repository\Values\User\Policy $policy */
            foreach ($permissionSet['policies'] as $policy) {
                $policyLimitations = $policy->getLimitations();
                if (!empty($policyLimitations)) {
                    foreach ($policyLimitations as $policyLimitation) {
                        $limitations[] = $policyLimitation;
                    }
                }
            }
        }

        return $limitations;
    }
}
