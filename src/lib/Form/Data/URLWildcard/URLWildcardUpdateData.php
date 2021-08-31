<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\URLWildcard;

use eZ\Publish\API\Repository\Values\Content\URLWildcard;

class URLWildcardUpdateData extends URLWildcardData
{
    /** @var \eZ\Publish\API\Repository\Values\Content\URLWildcard|null */
    private $urlWildcard;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\URLWildcard|null $urlWildcard
     */
    public function __construct(?URLWildcard $urlWildcard = null)
    {
        if ($urlWildcard instanceof URLWildcard) {
            parent::__construct($urlWildcard);
            $this->urlWildcard = $urlWildcard;
        }
    }

    /** @return \eZ\Publish\API\Repository\Values\Content\URLWildcard|null */
    public function getUrlWildcard(): ?URLWildcard
    {
        return $this->urlWildcard;
    }

    /** @param \eZ\Publish\API\Repository\Values\Content\URLWildcard|null $urlWildcard */
    public function setUrlWildcard(?URLWildcard $urlWildcard): void
    {
        $this->urlWildcard = $urlWildcard;
    }
}
