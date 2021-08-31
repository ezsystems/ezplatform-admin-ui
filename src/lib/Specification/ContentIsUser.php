<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Specification;

use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\Content;

class ContentIsUser implements ContentSpecification
{
    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /**
     * @param \eZ\Publish\API\Repository\UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Checks if $contentId is an existing User content.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     *
     * @return bool
     */
    public function isSatisfiedBy(Content $content): bool
    {
        return $this->userService->isUser($content);
    }
}
