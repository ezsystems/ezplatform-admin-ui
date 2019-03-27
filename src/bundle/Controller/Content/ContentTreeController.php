<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller\Content;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\Core\REST\Common\Message;
use eZ\Publish\Core\REST\Server\Controller as RestController;
use EzSystems\EzPlatformAdminUi\REST\Value\ContentTree\LoadSubtreeRequestNode;
use EzSystems\EzPlatformAdminUi\REST\Value\ContentTree\Node;
use EzSystems\EzPlatformAdminUi\REST\Value\ContentTree\Root;
use EzSystems\EzPlatformAdminUi\UI\Module\ContentTree\NodeFactory;
use Symfony\Component\HttpFoundation\Request;

class ContentTreeController extends RestController
{
    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Module\ContentTree\NodeFactory */
    private $contentTreeNodeFactory;

    /**
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \EzSystems\EzPlatformAdminUi\UI\Module\ContentTree\NodeFactory $contentTreeNodeFactory
     */
    public function __construct(
        LocationService $locationService,
        NodeFactory $contentTreeNodeFactory
    ) {
        $this->locationService = $locationService;
        $this->contentTreeNodeFactory = $contentTreeNodeFactory;
    }

    /**
     * @param int $parentLocationId
     * @param int $limit
     * @param int $offset
     *
     * @return \EzSystems\EzPlatformAdminUi\REST\Value\ContentTree\Node
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function loadChildrenAction(int $parentLocationId, int $limit, int $offset): Node
    {
        $location = $this->locationService->loadLocation($parentLocationId);

        $loadSubtreeRequestNode = new LoadSubtreeRequestNode($parentLocationId, $limit, $offset);

        return $this->contentTreeNodeFactory->createNode($location, $loadSubtreeRequestNode, true);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \EzSystems\EzPlatformAdminUi\REST\Value\ContentTree\Root
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function loadSubtreeAction(Request $request): Root
    {
        /** @var \EzSystems\EzPlatformAdminUi\REST\Value\ContentTree\LoadSubtreeRequest $loadSubtreeRequest */
        $loadSubtreeRequest = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        $locationIdList = array_column($loadSubtreeRequest->nodes, 'locationId');
        $locations = $this->locationService->loadLocationList($locationIdList);

        $elements = [];
        foreach ($loadSubtreeRequest->nodes as $childLoadSubtreeRequestNode) {
            // avoid errors caused by i.e. permissions change
            if (!array_key_exists($childLoadSubtreeRequestNode->locationId, $locations)) {
                continue;
            }

            $location = $locations[$childLoadSubtreeRequestNode->locationId];
            $elements[] = $this->contentTreeNodeFactory->createNode(
                $location,
                $childLoadSubtreeRequestNode,
                true
            );
        }

        return new Root($elements);
    }
}
