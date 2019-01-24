<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Specification;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\UserService;

class ContentIsUser implements ContentSpecification
{
    /** @var UserService */
    private $userService;

    /**
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Checks if $contentId is an existing User content.
     *
     * @param Content $content
     *
     * @return bool
     */
    public function isSatisfiedBy(Content $content): bool
    {
        return $this->userService->isUser($content);
    }
}
