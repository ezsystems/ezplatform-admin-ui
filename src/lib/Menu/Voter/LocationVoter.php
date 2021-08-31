<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
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
    private const CONTENT_VIEW_ROUTE_NAME = '_ez_content_view';

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @inheritdoc
     */
    public function matchItem(ItemInterface $item): ?bool
    {
        $routes = $item->getExtra('routes', []);

        foreach ($routes as $route) {
            if (isset($route['route']) && $route['route'] === self::CONTENT_VIEW_ROUTE_NAME) {
                $request = $this->requestStack->getCurrentRequest();
                $contentView = $request->attributes->get('view');
                $locationId = $route['parameters']['locationId'];

                if ($contentView instanceof ContentView && in_array($locationId, $contentView->getLocation()->path ?? [$contentView->getLocation()->id])) {
                    return true;
                }
            }
        }

        return null;
    }
}
