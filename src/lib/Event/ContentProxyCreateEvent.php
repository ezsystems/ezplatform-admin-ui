<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Event;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

class ContentProxyCreateEvent extends Event
{
    /** @var \Symfony\Component\HttpFoundation\Response|null */
    protected $response;

    /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType */
    protected $contentType;

    /** @var string */
    protected $languageCode;

    /** @var int */
    protected $parentLocationId;

    public function __construct(
        ContentType $contentType,
        string $languageCode,
        int $parentLocationId
    ) {
        $this->contentType = $contentType;
        $this->languageCode = $languageCode;
        $this->parentLocationId = $parentLocationId;
    }

    public function getContentType(): ContentType
    {
        return $this->contentType;
    }

    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    public function getParentLocationId(): int
    {
        return $this->parentLocationId;
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
        return !empty($this->response);
    }
}
