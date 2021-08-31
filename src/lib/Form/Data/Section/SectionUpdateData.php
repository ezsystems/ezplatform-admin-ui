<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Section;

use eZ\Publish\API\Repository\Values\Content\Section;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @todo add validation
 */
class SectionUpdateData
{
    /** @var \eZ\Publish\API\Repository\Values\Content\Section|null */
    protected $section;

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
     * @param \eZ\Publish\API\Repository\Values\Content\Section|null $section
     */
    public function __construct(?Section $section = null)
    {
        $this->section = $section;

        if (null !== $section) {
            $this->identifier = $section->identifier;
            $this->name = $section->name;
        }
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
     */
    public function setIdentifier(?string $identifier)
    {
        $this->identifier = $identifier;
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
     */
    public function setName(?string $name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getSection(): ?Section
    {
        return $this->section;
    }

    /**
     * @param mixed $section
     */
    public function setSection(?Section $section = null)
    {
        $this->section = $section;
    }
}
