<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\URLWildcard;

final class URLWildcardDeleteData
{
    /** @var bool[]|null */
    private $urlWildcardsChoices;

    public function __construct(?array $urlWildcardsChoices = [])
    {
        $this->urlWildcardsChoices = $urlWildcardsChoices;
    }

    /**
     * @return bool[]|null
     */
    public function getUrlWildcardsChoices(): ?array
    {
        return $this->urlWildcardsChoices;
    }

    /**
     * @param bool[]|null $urlWildcardsChoices
     */
    public function setUrlWildcardsChoices(?array $urlWildcardsChoices): void
    {
        $this->urlWildcardsChoices = $urlWildcardsChoices;
    }
}
