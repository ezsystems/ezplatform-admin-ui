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
}
