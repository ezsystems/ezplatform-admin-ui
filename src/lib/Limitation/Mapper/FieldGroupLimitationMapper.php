<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Limitation\Mapper;

use eZ\Publish\API\Repository\Values\User\Limitation;
use eZ\Publish\Core\Helper\FieldsGroups\FieldsGroupsList;
use EzSystems\EzPlatformAdminUi\Limitation\LimitationValueMapperInterface;

class FieldGroupLimitationMapper extends MultipleSelectionBasedMapper implements LimitationValueMapperInterface
{
    /** @var \eZ\Publish\Core\Helper\FieldsGroups\FieldsGroupsList */
    private $fieldsGroupsList;

    public function __construct(
        FieldsGroupsList $fieldsGroupsList
    ) {
        $this->fieldsGroupsList = $fieldsGroupsList;
    }

    protected function getSelectionChoices()
    {
        return $this->fieldsGroupsList->getGroups();
    }

    public function mapLimitationValue(Limitation $limitation)
    {
        return $limitation->limitationValues;
    }
}
