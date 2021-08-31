<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Section;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @todo add validation
 */
class SectionCreateData
{
    /**
     * @var string|null
     *
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^[[:alnum:]_]+$/",
     *     message="ez.section.identifier.format"
     * )
     */
    protected $identifier;

    /**
     * @var string|null
     *
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @param string|null $identifier
     * @param string|null $name
     */
    public function __construct(?string $identifier = null, ?string $name = null)
    {
        $this->identifier = $identifier;
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * @param string|null $identifier
     *
     * @return SectionCreateData
     */
    public function setIdentifier(?string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     *
     * @return SectionCreateData
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
