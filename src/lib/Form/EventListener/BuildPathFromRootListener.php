<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\EventListener;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\URLAliasService;
use Symfony\Component\Form\FormEvent;

class BuildPathFromRootListener
{
    /** @var LocationService */
    private $locationService;

    /** @var URLAliasService */
    private $urlAliasService;

    /**
     * BuildPathFromRootListener constructor.
     *
     * @param LocationService $locationService
     * @param URLAliasService $urlAliasService
     */
    public function __construct(LocationService $locationService, URLAliasService $urlAliasService)
    {
        $this->locationService = $locationService;
        $this->urlAliasService = $urlAliasService;
    }

    /**
     * @param FormEvent $event
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function onPreSubmitData(FormEvent $event)
    {
        $data = $event->getData();
        if (!array_key_exists('site_root', $data) || false === (bool)$data['site_root']) {
            $location = $this->locationService->loadLocation($data['location']);
            $urlAlias = $this->urlAliasService->reverseLookup($location);
            $data['path'] = $urlAlias->path . '/' . $data['path'];
            $event->setData($data);
        }
    }
}
