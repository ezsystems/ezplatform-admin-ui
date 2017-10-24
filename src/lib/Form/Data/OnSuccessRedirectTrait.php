<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data;

trait OnSuccessRedirectTrait
{
    /** @var string */
    protected $onSuccessRedirectionUrl;

    /**
     * @param null|string $url
     */
    public function setOnSuccessRedirectionUrl(?string $url): void
    {
        $this->onSuccessRedirectionUrl = $url;
    }

    /**
     * @return null|string
     */
    public function getOnSuccessRedirectionUrl(): ?string
    {
        return $this->onSuccessRedirectionUrl;
    }
}
