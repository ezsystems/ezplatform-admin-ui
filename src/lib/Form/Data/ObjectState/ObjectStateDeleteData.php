<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\ObjectState;

use eZ\Publish\API\Repository\Values\ObjectState\ObjectState;

class ObjectStateDeleteData
{
    /**
     * @var \eZ\Publish\API\Repository\Values\ObjectState\ObjectState|null
     */
    private $objectState;

    /**
     * @param \eZ\Publish\API\Repository\Values\ObjectState\ObjectState|null $objectState
     */
    public function __construct(?ObjectState $objectState = null)
    {
        $this->objectState = $objectState;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\ObjectState\ObjectState
     */
    public function getObjectState(): ObjectState
    {
        return $this->objectState;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\ObjectState\ObjectState $objectState
     */
    public function setObjectState(ObjectState $objectState)
    {
        $this->objectState = $objectState;
    }
}
