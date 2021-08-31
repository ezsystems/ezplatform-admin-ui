<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Limitation\Mapper;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\User\Limitation;
use EzSystems\EzPlatformAdminUi\Limitation\LimitationValueMapperInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

class ContentTypeLimitationMapper extends MultipleSelectionBasedMapper implements LimitationValueMapperInterface
{
    use LoggerAwareTrait;

    /**
     * @var \eZ\Publish\API\Repository\ContentTypeService
     */
    private $contentTypeService;

    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
        $this->logger = new NullLogger();
    }

    protected function getSelectionChoices()
    {
        $contentTypeChoices = [];
        foreach ($this->contentTypeService->loadContentTypeGroups() as $group) {
            foreach ($this->contentTypeService->loadContentTypes($group) as $contentType) {
                $contentTypeChoices[$contentType->id] = $contentType->getName($contentType->mainLanguageCode);
            }
        }

        return $contentTypeChoices;
    }

    public function mapLimitationValue(Limitation $limitation)
    {
        $values = [];
        foreach ($limitation->limitationValues as $contentTypeId) {
            try {
                $values[] = $this->contentTypeService->loadContentType($contentTypeId);
            } catch (NotFoundException $e) {
                $this->logger->error(sprintf('Could not map the Limitation value: could not find a Content Type with ID %s', $contentTypeId));
            }
        }

        return $values;
    }
}
