<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\View;

use eZ\Publish\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

class ContentTranslateSuccessView extends BaseView
{
    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    public function __construct(Response $response)
    {
        parent::__construct('@ezdesign/http/no_content.html.twig');

        $this->setResponse($response);
        $this->setControllerReference(new ControllerReference('EzSystems\EzPlatformAdminUiBundle\Controller\ContentEditController::translationSuccessAction'));
    }
}
