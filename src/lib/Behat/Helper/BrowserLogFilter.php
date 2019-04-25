<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\Helper;

class BrowserLogFilter
{
    private $excludedPatterns = [
        '@.*/api/ezp/v2/bookmark/.* - Failed to load resource: the server responded with a status of 404 \(Not Found\)@',
        '@.*/bundles/netgentags/admin/jstree/js/jstree.min.js 5 document.registerElement is deprecated and will be removed in M73, around March 2019. Please use window.customElements.define instead. See https://www.chromestatus.com/features/4642138092470272 for more details.@',
        '@.*/admin/version-draft/has-no-conflict/.* - Failed to load resource: the server responded with a status of 409 \(Conflict\)@',
        '@webpack:///./vendor/ezsystems/ezplatform-admin-ui/src/bundle/Resources/public/js/scripts/fieldType/ezobjectrelationlist.js\? 91:12 "EzObjectRelation fieldtype is deprecated. Please, use EzObjectRelationList fieldtype instead."@',
    ];

    public function filter(array $logEntries): array
    {
        return array_values(array_filter($logEntries, function ($logEntry) {
            foreach ($this->excludedPatterns as $excludedPattern) {
                if (preg_match($excludedPattern, $logEntry)) {
                    return false;
                }
            }

            return true;
        }));
    }
}
