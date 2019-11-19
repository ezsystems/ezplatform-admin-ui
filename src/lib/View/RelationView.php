<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\View;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\MVC\Symfony\View\BaseView;
use Exception;

/**
 * {@inheritdoc}
 */
class RelationView extends BaseView
{
    /** @var \eZ\Publish\API\Repository\Values\Content\Content|null */
    private $content;

    /** @var \eZ\Publish\API\Repository\Values\Content\Location|null */
    private $location;

    /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType|null */
    private $contentType;

    /** @var \Exception */
    private $apiException;

    /** @var int */
    private $contentId;

    public function setContentId(int $contentId): void
    {
        $this->contentId = $contentId;
    }

    public function getContentId(): int
    {
        return $this->contentId;
    }

    public function setContent(Content $content): void
    {
        $this->content = $content;
    }

    public function getContent(): ?Content
    {
        return $this->content;
    }

    public function setLocation(?Location $location): void
    {
        $this->location = $location;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function getApiException(): ?Exception
    {
        return $this->apiException;
    }

    public function setApiException(?Exception $apiException): void
    {
        $this->apiException = $apiException;
    }

    public function getContentType(): ?ContentType
    {
        return $this->contentType;
    }

    public function setContentType(?ContentType $contentType): void
    {
        $this->contentType = $contentType;
    }
}
