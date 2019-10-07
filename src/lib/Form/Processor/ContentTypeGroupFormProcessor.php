<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Processor;

use eZ\Publish\API\Repository\ContentTypeService;
use EzSystems\EzPlatformAdminUi\Event\FormActionEvent;
use EzSystems\EzPlatformAdminUi\Event\RepositoryFormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ContentTypeGroupFormProcessor implements EventSubscriberInterface
{
    /**
     * @var ContentTypeService
     */
    private $contentTypeService;

    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    public static function getSubscribedEvents()
    {
        return [
            RepositoryFormEvents::CONTENT_TYPE_GROUP_UPDATE => ['processUpdate', 10],
        ];
    }

    public function processUpdate(FormActionEvent $event)
    {
        /** @var \EzSystems\EzPlatformAdminUi\RepositoryForms\Data\ContentTypeGroup\ContentTypeGroupCreateData|\EzSystems\EzPlatformAdminUi\RepositoryForms\Data\ContentTypeGroup\ContentTypeGroupUpdateData $data */
        $data = $event->getData();
        if ($data->isNew()) {
            $contentTypeGroup = $this->contentTypeService->createContentTypeGroup($data);
        } else {
            $this->contentTypeService->updateContentTypeGroup($data->contentTypeGroup, $data);
            $contentTypeGroup = $this->contentTypeService->loadContentTypeGroup($data->getId());
        }

        $data->setContentTypeGroup($contentTypeGroup);
    }
}
