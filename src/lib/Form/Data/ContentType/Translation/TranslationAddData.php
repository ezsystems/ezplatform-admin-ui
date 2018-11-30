<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\ContentType\Translation;

use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup;
use Symfony\Component\Validator\Constraints as Assert;

class TranslationAddData
{
    /**
     * @Assert\NotBlank()
     *
     * @var \eZ\Publish\API\Repository\Values\ContentType\ContentType|null
     */
    protected $contentType;

    /**
     * @Assert\NotBlank()
     *
     * @var \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup|null
     */
    protected $contentTypeGroup;

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
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType|null $contentType
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup|null $contentTypeGroup
     * @param \eZ\Publish\API\Repository\Values\Content\Language|null $language
     * @param \eZ\Publish\API\Repository\Values\Content\Language|null $baseLanguage
     */
    public function __construct(
        ContentType $contentType = null,
        ContentTypeGroup $contentTypeGroup = null,
        Language $language = null,
        Language $baseLanguage = null
    ) {
        $this->contentType = $contentType;
        $this->contentTypeGroup = $contentTypeGroup;
        $this->language = $language;
        $this->baseLanguage = $baseLanguage;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentType|null
     */
    public function getContentType(): ?ContentType
    {
        return $this->contentType;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType|null $contentType
     *
     * @return self
     */
    public function setContentType(ContentType $contentType): self
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @return ContentTypeGroup|null
     */
    public function getContentTypeGroup(): ?ContentTypeGroup
    {
        return $this->contentTypeGroup;
    }

    /**
     * @param ContentTypeGroup $contentTypeGroup
     *
     * @return self
     */
    public function setContentTypeGroup(ContentTypeGroup $contentTypeGroup): self
    {
        $this->contentTypeGroup = $contentTypeGroup;

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
     * @param \eZ\Publish\API\Repository\Values\Content\Language|null $language
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
     * @param \eZ\Publish\API\Repository\Values\Content\Language|null $baseLanguage
     *
     * @return self
     */
    public function setBaseLanguage(Language $baseLanguage): self
    {
        $this->baseLanguage = $baseLanguage;

        return $this;
    }
}
