<?php
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