<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Language;

/**
 * @todo Add validation
 */
class LanguagesDeleteData
{
    /** @var array|null */
    protected $languages;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Language[]|null $languages
     */
    public function __construct(array $languages = [])
    {
        $this->languages = $languages;
    }

    /**
     * @return array|null
     */
    public function getLanguages(): ?array
    {
        return $this->languages;
    }

    /**
     * @param array|null $languages
     */
    public function setLanguages(?array $languages)
    {
        $this->languages = $languages;
    }
}
