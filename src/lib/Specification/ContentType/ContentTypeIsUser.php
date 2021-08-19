<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Specification\ContentType;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Ibexa\AdminUi\Specification\AbstractSpecification;
use Ibexa\AdminUi\Exception\InvalidArgumentException;

class ContentTypeIsUser extends AbstractSpecification
{
    private const EZUSER_FIELD_TYPE_IDENTIFIER = 'ezuser';

    /** @var array */
    private $userContentTypeIdentifier;

    /**
     * @param array $userContentTypeIdentifier
     */
    public function __construct(array $userContentTypeIdentifier)
    {
        $this->userContentTypeIdentifier = $userContentTypeIdentifier;
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
            throw new InvalidArgumentException('$contentType', sprintf('Must be an instance of %s', ContentType::class));
        }

        if (in_array($contentType->identifier, $this->userContentTypeIdentifier, true)) {
            return true;
        }

        return $contentType->hasFieldDefinitionOfType(self::EZUSER_FIELD_TYPE_IDENTIFIER);
    }
}

class_alias(ContentTypeIsUser::class, 'EzSystems\EzPlatformAdminUi\Specification\ContentType\ContentTypeIsUser');
