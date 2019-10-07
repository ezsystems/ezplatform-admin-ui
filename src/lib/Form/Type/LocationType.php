<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\LocationService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class LocationType extends AbstractType
{
    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /**
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     */
    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['destination_location'] = null;

        if ($view->vars['value']) {
            try {
                $view->vars['destination_location'] = $this->locationService->loadLocation(
                    (int)$view->vars['value']
                );
            } catch (NotFoundException | UnauthorizedException $e) {
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return HiddenType::class;
    }
}
