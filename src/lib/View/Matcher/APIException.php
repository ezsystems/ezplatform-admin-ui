<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\View\Matcher;

use eZ\Publish\Core\MVC\Symfony\Matcher\ViewMatcherInterface;
use eZ\Publish\Core\MVC\Symfony\View\View;
use EzSystems\EzPlatformAdminUi\View\RelationView;

/**
 * Match based on the user setting identifier.
 */
class APIException implements ViewMatcherInterface
{
    /** @var string */
    private $apiException;

    /**
     * {@inheritdoc}
     */
    public function setMatchingConfig($matchingConfig): void
    {
        $this->apiException = $matchingConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function match(View $view): bool
    {
        if (!$view instanceof RelationView || $view->getApiException() === null) {
            return false;
        }

        return $view->getApiException() === $this->apiException;
    }
}
