<?php

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
     */
    public function setIdentifier(?string $identifier)
    {
        $this->identifier = $identifier;
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
     */
    public function setName(?string $name)
    {
        $this->name = $name;
    }
}
