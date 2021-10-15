<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\AdminUi\Controller;

use Ibexa\Contracts\AdminUi\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

final class URLManagementController extends Controller
{
    public function urlManagementAction(): Response
    {
        return $this->render('@ezdesign/url_management/url_management.html.twig');
    }
}

class_alias(URLManagementController::class, 'EzSystems\EzPlatformAdminUiBundle\Controller\URLManagementController');
