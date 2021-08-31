<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Util;

use eZ\Publish\Core\Helper\FieldsGroups\FieldsGroupsList;

class FieldDefinitionGroupsUtil
{
    /** @var \eZ\Publish\Core\Helper\FieldsGroups\FieldsGroupsList */
    private $fieldsGroupsListHelper;

    /**
     * @param \eZ\Publish\Core\Helper\FieldsGroups\FieldsGroupsList $fieldsGroupsListHelper
     */
    public function __construct(FieldsGroupsList $fieldsGroupsListHelper)
    {
        $this->fieldsGroupsListHelper = $fieldsGroupsListHelper;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition[] $fieldDefinitions
     *
     * @return array
     */
    public function groupFieldDefinitions(iterable $fieldDefinitions): array
    {
        $fieldDefinitionsByGroup = [];
        foreach ($this->fieldsGroupsListHelper->getGroups() as $groupId => $groupName) {
            $fieldDefinitionsByGroup[$groupId] = [
                'name' => $groupName,
                'fieldDefinitions' => [],
            ];
        }

        foreach ($fieldDefinitions as $fieldDefinition) {
            $groupId = $fieldDefinition->fieldGroup;
            if (!$groupId) {
                $groupId = $this->fieldsGroupsListHelper->getDefaultGroup();
            }

            $fieldDefinitionsByGroup[$groupId]['fieldDefinitions'][] = $fieldDefinition;
            $fieldDefinitionsByGroup[$groupId]['name'] = $fieldDefinitionsByGroup[$groupId]['name'] ?? $groupId;
        }

        return $fieldDefinitionsByGroup;
    }
}
