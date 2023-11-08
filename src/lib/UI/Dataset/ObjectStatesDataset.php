<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Dataset;

use eZ\Publish\API\Repository\ObjectStateService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup;
use EzSystems\EzPlatformAdminUi\UI\Value as UIValue;
use EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory;

class ObjectStatesDataset
{
    /** @var \eZ\Publish\API\Repository\ObjectStateService */
    protected $objectStateService;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory */
    protected $valueFactory;

    /** @var UIValue\ObjectState\ObjectState[] */
    protected $data;

    public function __construct(ObjectStateService $objectStateService, ValueFactory $valueFactory)
    {
        $this->objectStateService = $objectStateService;
        $this->valueFactory = $valueFactory;
    }

    public function load(ContentInfo $contentInfo, Location $location): self
    {
        $data = array_map(
            function (ObjectStateGroup $objectStateGroup) use ($contentInfo, $location) {
                $hasObjectStates = !empty($this->objectStateService->loadObjectStates($objectStateGroup));
                if (!$hasObjectStates) {
                    return [];
                }

                return $this->valueFactory->createObjectState($contentInfo, $objectStateGroup, $location);
            },
            $this->objectStateService->loadObjectStateGroups()
        );

        // Get rid of empty Object State Groups
        $this->data = array_filter($data);

        return $this;
    }

    /**
     * @return UIValue\ObjectState\ObjectState[]
     */
    public function getObjectStates(): array
    {
        return $this->data;
    }
}
