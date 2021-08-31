<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\ObjectState;

use eZ\Publish\API\Repository\Values\ObjectState\ObjectState;

class ObjectStateUpdateData
{
    /**
     * @var \eZ\Publish\API\Repository\Values\ObjectState\ObjectState|null
     */
    private $objectState;

    /** @var string */
    private $identifier;

    /** @var string */
    private $name;

    /**
     * @param \eZ\Publish\API\Repository\Values\ObjectState\ObjectState|null $objectState
     */
    public function __construct(?ObjectState $objectState = null)
    {
        if ($objectState instanceof ObjectState) {
            $this->objectState = $objectState;
            $this->name = $objectState->getName();
            $this->identifier = $objectState->identifier;
        }
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier(string $identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
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
