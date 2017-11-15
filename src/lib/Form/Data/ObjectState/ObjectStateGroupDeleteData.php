<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\ObjectState;

use eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup;

class ObjectStateGroupDeleteData
{
    /** @var ObjectStateGroup */
    private $objectStateGroup;

    public function __construct(?ObjectStateGroup $objectStateGroup = null)
    {
        $this->objectStateGroup = $objectStateGroup;
    }

    /**
     * @return ObjectStateGroup
     */
    public function getObjectStateGroup(): ObjectStateGroup
    {
        return $this->objectStateGroup;
    }

    /**
     * @param ObjectStateGroup $objectStateGroup
     */
    public function setObjectStateGroup(ObjectStateGroup $objectStateGroup)
    {
        $this->objectStateGroup = $objectStateGroup;
    }
}
