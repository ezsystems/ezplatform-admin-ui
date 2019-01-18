<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Specification;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;

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
        if (method_exists($this->userService, 'isUser')) {
            return $this->userService->isUser($content);
        }

        // @deprecated As of 2.4 UserService should be able to tell us this above
        return $content->getVersionInfo()->getContentInfo()->contentTypeId === 4;
    }
}
