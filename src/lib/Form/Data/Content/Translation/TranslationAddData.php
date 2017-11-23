<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Content\Translation;

use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Component\Validator\Constraints as Assert;

class TranslationAddData
{
    /**
     * @Assert\NotBlank()
     *
     * @var Location|null
     */
    protected $location;

    /**
     * @Assert\NotBlank()
     *
     * @var Language|null
     */
    protected $language;

    /**
     * @var Language|null
     */
    protected $baseLanguage;

    /**
     * @param Location|null $location
     * @param Language|null $language
     * @param Language|null $baseLanguage
     */
    public function __construct(
        Location $location = null,
        Language $language = null,
        Language $baseLanguage = null
    ) {
        $this->location = $location;
        $this->language = $language;
        $this->baseLanguage = $baseLanguage;
    }

    /**
     * @return Location|null
     */
    public function getLocation(): ?Location
    {
        return $this->location;
    }

    /**
     * @param Location $location
     *
     * @return self
     */
    public function setLocation(Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return Language|null
     */
    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    /**
     * @param Language $language
     *
     * @return self
     */
    public function setLanguage(Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return Language|null
     */
    public function getBaseLanguage(): ?Language
    {
        return $this->baseLanguage;
    }

    /**
     * @param Language $baseLanguage
     *
     * @return self
     */
    public function setBaseLanguage(Language $baseLanguage): self
    {
        $this->baseLanguage = $baseLanguage;

        return $this;
    }
}
