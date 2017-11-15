<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Dataset;

use eZ\Publish\API\Repository\ObjectStateService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup;
use EzSystems\EzPlatformAdminUi\UI\Value as UIValue;
use EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory;

class ObjectStatesDataset
{
    /** @var ObjectStateService */
    protected $objectStateService;

    /** @var ValueFactory */
    protected $valueFactory;

    /** @var UIValue\ObjectState\ObjectState[] */
    protected $data;

    /**
     * @param ObjectStateService $objectStateService
     * @param ValueFactory $valueFactory
     */
    public function __construct(ObjectStateService $objectStateService, ValueFactory $valueFactory)
    {
        $this->objectStateService = $objectStateService;
        $this->valueFactory = $valueFactory;
    }

    /**
     * @param ContentInfo $contentInfo
     *
     * @return ObjectStatesDataset
     */
    public function load(ContentInfo $contentInfo): self
    {
        $this->data = array_map(
            function (ObjectStateGroup $objectStateGroup) use ($contentInfo) {
                return $this->valueFactory->createObjectState($contentInfo, $objectStateGroup);
            },
            $this->objectStateService->loadObjectStateGroups()
        );

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
