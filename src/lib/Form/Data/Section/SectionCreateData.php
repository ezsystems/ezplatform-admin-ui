<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Section;

/**
 * @todo add validation
 */
class SectionCreateData
{
    /** @var string|null */
    protected $identifier;

    /** @var string|null */
    protected $name;

    /**
     * @param null|string $identifier
     * @param null|string $name
     */
    public function __construct(?string $identifier = null, ?string $name = null)
    {
        $this->identifier = $identifier;
        $this->name = $name;
    }

    /**
     * @return null|string
     */
    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * @param null|string $identifier
     *
     * @return SectionCreateData
     */
    public function setIdentifier(?string $identifier): SectionCreateData
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     *
     * @return SectionCreateData
     */
    public function setName(?string $name): SectionCreateData
    {
        $this->name = $name;

        return $this;
    }
}
