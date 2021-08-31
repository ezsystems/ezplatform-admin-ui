<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Specification\ContentType;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;
use EzSystems\EzPlatformAdminUi\Specification\AbstractSpecification;

class ContentTypeIsUserGroup extends AbstractSpecification
{
    /** @var array */
    private $userGroupContentTypeIdentifier;

    /**
     * @param array $userGroupContentTypeIdentifier
     */
    public function __construct(array $userGroupContentTypeIdentifier)
    {
        $this->userGroupContentTypeIdentifier = $userGroupContentTypeIdentifier;
    }

    /**
     * Checks if $contentType is an existing User content.
     *
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType
     *
     * @return bool
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     */
    public function isSatisfiedBy($contentType): bool
    {
        if (!$contentType instanceof ContentType) {
            throw new InvalidArgumentException($contentType, sprintf('Must be an instance of %s', ContentType::class));
        }

        return in_array($contentType->identifier, $this->userGroupContentTypeIdentifier, true);
    }
}
