<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\ObjectState;

class ObjectStatesDeleteData
{
    /** @var \eZ\Publish\API\Repository\Values\ObjectState\ObjectState[]|null */
    protected $objectStates;

    /**
     * @param \eZ\Publish\API\Repository\Values\ObjectState\ObjectState[]|null $objectStates
     */
    public function __construct(array $objectStates = [])
    {
        $this->objectStates = $objectStates;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\ObjectState\ObjectState[]|null
     */
    public function getObjectStates(): ?array
    {
        return $this->objectStates;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\ObjectState\ObjectState[]|null $objectStates
     */
    public function setObjectStates(?array $objectStates)
    {
        $this->objectStates = $objectStates;
    }
}
