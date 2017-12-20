<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Menu\Voter;

use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Voter\VoterInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class LocationVoter implements VoterInterface
{
    private const EZ_PUBLISH_LOCATION_ROUTE_NAME = '_ezpublishLocation';

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function matchItem(ItemInterface $item)
    {
        $route = $item->getExtra('routes')[0];
        $contentView = $this->requestStack->getCurrentRequest()->attributes->get('view');

        if ($route['route'] === self::EZ_PUBLISH_LOCATION_ROUTE_NAME && $contentView instanceof ContentView) {
            if ((int)$contentView->getLocation()->path[1] === $route['parameters']['locationId']) {
                return true;
            }
        }

        return false;
    }
}
