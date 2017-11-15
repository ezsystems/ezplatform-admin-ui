<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\ObjectState;

use eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup;

class ObjectStateGroupUpdateData
{
    /** @var ObjectStateGroup */
    private $objectStateGroup;

    /** @var string */
    private $identifier;

    /** @var string */
    private $name;

    public function __construct(?ObjectStateGroup $objectStateGroup = null)
    {
        if ($objectStateGroup instanceof ObjectStateGroup) {
            $this->objectStateGroup = $objectStateGroup;
            $this->name = $objectStateGroup->getName();
            $this->identifier = $objectStateGroup->identifier;
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
