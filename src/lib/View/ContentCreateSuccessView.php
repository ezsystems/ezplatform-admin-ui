<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\View;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\Symfony\View\BaseView;
use eZ\Publish\Core\MVC\Symfony\View\LocationValueView;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

class ContentCreateSuccessView extends BaseView implements LocationValueView
{
    /** @var \eZ\Publish\API\Repository\Values\Content\Location|null */
    private $location;

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    public function __construct(Response $response)
    {
        parent::__construct('@ezdesign/http/302_empty_content.html.twig');

        $this->setResponse($response);
        $this->setControllerReference(new ControllerReference('EzSystems\EzPlatformAdminUiBundle\Controller\ContentEditController::createWithoutDraftSuccessAction'));
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location|null $location
     */
    public function setLocation(?Location $location): void
    {
        $this->location = $location;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Location|null
     */
    public function getLocation(): ?Location
    {
        return $this->location;
    }
}
