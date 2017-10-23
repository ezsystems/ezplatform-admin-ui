<?php
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data;

trait OnFailureRedirectTrait
{
    /** @var string */
    protected $onFailureRedirectionUrl;

    /**
     * @param null|string $url
     */
    public function setOnFailureRedirectionUrl(?string $url): void
    {
        $this->onFailureRedirectionUrl = $url;
    }

    /**
     * @return null|string
     */
    public function getOnFailureRedirectionUrl(): ?string
    {
        return $this->onFailureRedirectionUrl;
    }
}