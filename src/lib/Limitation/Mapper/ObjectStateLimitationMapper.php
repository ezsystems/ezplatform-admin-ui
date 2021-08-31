<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Limitation\Mapper;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\ObjectStateService;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectState;
use eZ\Publish\API\Repository\Values\User\Limitation;
use EzSystems\EzPlatformAdminUi\Limitation\LimitationValueMapperInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

class ObjectStateLimitationMapper extends MultipleSelectionBasedMapper implements LimitationValueMapperInterface
{
    use LoggerAwareTrait;

    /**
     * @var \eZ\Publish\API\Repository\ObjectStateService
     */
    private $objectStateService;

    public function __construct(ObjectStateService $objectStateService)
    {
        $this->objectStateService = $objectStateService;
        $this->logger = new NullLogger();
    }

    protected function getSelectionChoices()
    {
        $choices = [];
        foreach ($this->objectStateService->loadObjectStateGroups() as $group) {
            foreach ($this->objectStateService->loadObjectStates($group) as $state) {
                $choices[$state->id] = $this->getObjectStateLabel($state);
            }
        }

        return $choices;
    }

    public function mapLimitationValue(Limitation $limitation)
    {
        $values = [];

        foreach ($limitation->limitationValues as $stateId) {
            try {
                $values[] = $this->getObjectStateLabel(
                    $this->objectStateService->loadObjectState($stateId)
                );
            } catch (NotFoundException $e) {
                $this->logger->error(sprintf('Could not map the Limitation value: could not find an Object state with ID %s', $stateId));
            }
        }

        return $values;
    }

    protected function getObjectStateLabel(ObjectState $state)
    {
        $groupName = $state
            ->getObjectStateGroup()
            ->getName($state->defaultLanguageCode);

        $stateName = $state->getName($state->defaultLanguageCode);

        return $groupName . ':' . $stateName;
    }
}
