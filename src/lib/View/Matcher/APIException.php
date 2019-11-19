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
    /** @var \Exception */
    private $matchingException;

    /**
     * {@inheritdoc}
     */
    public function setMatchingConfig($matchingConfig): void
    {
        $this->matchingException = $matchingConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function match(View $view): bool
    {
        if (!$view instanceof RelationView || $view->getApiException() === null) {
            return false;
        }

        return get_class($view->getApiException()) === $this->matchingException;
    }
}
