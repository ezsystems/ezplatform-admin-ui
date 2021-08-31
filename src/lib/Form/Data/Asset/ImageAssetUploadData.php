<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Asset;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class ImageAssetUploadData
{
    /**
     * @Assert\NotBlank()
     * @Assert\Image(detectCorrupted = true)
     *
     * @var \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    private $file;

    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    private $languageCode;

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile|null $file
     * @param string|null $languageCode
     */
    public function __construct(?UploadedFile $file = null, string $languageCode = null)
    {
        $this->file = $file;
        $this->languageCode = $languageCode;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile|null
     */
    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file|null
     *
     * @return \EzSystems\EzPlatformAdminUi\Form\Data\Asset\ImageAssetUploadData
     */
    public function setFile(?UploadedFile $file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLanguageCode(): ?string
    {
        return $this->languageCode;
    }

    /**
     * @param string|null $languageCode
     *
     * @return \EzSystems\EzPlatformAdminUi\Form\Data\Asset\ImageAssetUploadData
     */
    public function setLanguageCode(?string $languageCode): self
    {
        $this->languageCode = $languageCode;

        return $this;
    }
}
