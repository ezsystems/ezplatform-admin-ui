<?php
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data;

interface OnFailureRedirect
{
    /**
     * @param null|string $url
     */
    public function setOnFailureRedirectionUrl(?string $url): void;

    /**
     * @return null|string
     */
    public function getOnFailureRedirectionUrl(): ?string;
}