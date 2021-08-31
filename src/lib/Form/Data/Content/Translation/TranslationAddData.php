<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
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
     * @var \eZ\Publish\API\Repository\Values\Content\Location|null
     */
    protected $location;

    /**
     * @Assert\NotBlank()
     *
     * @var \eZ\Publish\API\Repository\Values\Content\Language|null
     */
    protected $language;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Language|null
     */
    protected $baseLanguage;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location|null $location
     * @param \eZ\Publish\API\Repository\Values\Content\Language|null $language
     * @param \eZ\Publish\API\Repository\Values\Content\Language|null $baseLanguage
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
     * @return \eZ\Publish\API\Repository\Values\Content\Location|null
     */
    public function getLocation(): ?Location
    {
        return $this->location;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return self
     */
    public function setLocation(Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Language|null
     */
    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Language $language
     *
     * @return self
     */
    public function setLanguage(Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Language|null
     */
    public function getBaseLanguage(): ?Language
    {
        return $this->baseLanguage;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Language $baseLanguage
     *
     * @return self
     */
    public function setBaseLanguage(Language $baseLanguage): self
    {
        $this->baseLanguage = $baseLanguage;

        return $this;
    }
}
