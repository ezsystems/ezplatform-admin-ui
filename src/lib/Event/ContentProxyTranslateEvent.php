<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Event;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @internal
 */
class ContentProxyTranslateEvent extends Event
{
    /** @var \Symfony\Component\HttpFoundation\Response|null */
    private $response;

    /** @var int */
    private $contentId;

    /** @var string|null */
    private $fromLanguageCode;

    /** @var string */
    private $toLanguageCode;

    /** @var \EzSystems\EzPlatformAdminUi\Event\Options */
    private $options;

    public function __construct(
        int $contentId,
        ?string $fromLanguageCode,
        string $toLanguageCode,
        ?Options $options = null
    ) {
        $this->contentId = $contentId;
        $this->fromLanguageCode = $fromLanguageCode;
        $this->toLanguageCode = $toLanguageCode;
        $this->options = $options ?? new Options();
    }

    public function getContentId(): int
    {
        return $this->contentId;
    }

    public function getFromLanguageCode(): ?string
    {
        return $this->fromLanguageCode;
    }

    public function getToLanguageCode(): string
    {
        return $this->toLanguageCode;
    }

    public function getOptions(): Options
    {
        return $this->options;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }

    public function hasResponse(): bool
    {
        return isset($this->response);
    }
}
