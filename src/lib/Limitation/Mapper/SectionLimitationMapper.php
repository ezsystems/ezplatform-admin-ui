<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Limitation\Mapper;

use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\Values\User\Limitation;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use EzSystems\EzPlatformAdminUi\Limitation\LimitationValueMapperInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

class SectionLimitationMapper extends MultipleSelectionBasedMapper implements LimitationValueMapperInterface
{
    use LoggerAwareTrait;

    /**
     * @var SectionService
     */
    private $sectionService;

    public function __construct(SectionService $sectionService)
    {
        $this->sectionService = $sectionService;
        $this->logger = new NullLogger();
    }

    protected function getSelectionChoices()
    {
        $choices = [];
        foreach ($this->sectionService->loadSections() as $section) {
            $choices[$section->id] = $section->name;
        }

        return $choices;
    }

    public function mapLimitationValue(Limitation $limitation)
    {
        $values = [];
        foreach ($limitation->limitationValues as $sectionId) {
            try {
                $values[] = $this->sectionService->loadSection($sectionId);
            } catch (NotFoundException $e) {
                $this->logger->error(sprintf('Could not map limitation value: Section with id = %s not found', $sectionId));
            }
        }

        return $values;
    }
}
