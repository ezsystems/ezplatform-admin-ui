<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Processor\ContentType;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeDraft;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionCreateStruct;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\Helper\FieldsGroups\FieldsGroupsList;
use EzSystems\EzPlatformAdminUi\Event\FormEvents;
use EzSystems\EzPlatformContentForms\Event\FormActionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class ContentTypeFormProcessor implements EventSubscriberInterface
{
    /**
     * @var \eZ\Publish\API\Repository\ContentTypeService
     */
    private $contentTypeService;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @var array
     */
    private $options;

    /**
     * @var \eZ\Publish\Core\Helper\FieldsGroups\FieldsGroupsList
     */
    private $groupsList;

    public function __construct(ContentTypeService $contentTypeService, RouterInterface $router, array $options = [])
    {
        $this->contentTypeService = $contentTypeService;
        $this->router = $router;
        $this->setOptions($options);
    }

    public function setGroupsList(FieldsGroupsList $groupsList)
    {
        $this->groupsList = $groupsList;
    }

    public function setOptions(array $options = [])
    {
        $this->options = $options + ['redirectRouteAfterPublish' => null];
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::CONTENT_TYPE_UPDATE => 'processDefaultAction',
            FormEvents::CONTENT_TYPE_ADD_FIELD_DEFINITION => 'processAddFieldDefinition',
            FormEvents::CONTENT_TYPE_REMOVE_FIELD_DEFINITION => 'processRemoveFieldDefinition',
            FormEvents::CONTENT_TYPE_PUBLISH => 'processPublishContentType',
            FormEvents::CONTENT_TYPE_REMOVE_DRAFT => 'processRemoveContentTypeDraft',
        ];
    }

    public function processDefaultAction(FormActionEvent $event)
    {
        // Don't update anything if we just want to cancel the draft.
        if ($event->getClickedButton() === 'removeDraft') {
            return;
        }

        // Always update FieldDefinitions and ContentTypeDraft
        /** @var \EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeData $contentTypeData */
        $contentTypeData = $event->getData();
        $contentTypeDraft = $contentTypeData->contentTypeDraft;
        foreach ($contentTypeData->getFlatFieldDefinitionsData() as $fieldDefData) {
            $this->contentTypeService->updateFieldDefinition($contentTypeDraft, $fieldDefData->fieldDefinition, $fieldDefData);
        }
        $contentTypeData->sortFieldDefinitions();
        $this->contentTypeService->updateContentTypeDraft($contentTypeDraft, $contentTypeData);
    }

    public function processAddFieldDefinition(FormActionEvent $event)
    {
        // Reload the draft, to make sure we include any changes made in the current form submit
        $contentTypeDraft = $this->contentTypeService->loadContentTypeDraft($event->getData()->contentTypeDraft->id);
        $fieldTypeIdentifier = $event->getForm()->get('fieldTypeSelection')->getData();

        $targetLanguageCode = $event->getForm()->getConfig()->getOption('languageCode');
        if ($contentTypeDraft->mainLanguageCode !== $targetLanguageCode) {
            throw new InvalidArgumentException(
                'languageCode',
                'Field definitions can only be added to the main language translation'
            );
        }

        $maxFieldPos = 0;
        foreach ($contentTypeDraft->fieldDefinitions as $existingFieldDef) {
            if ($existingFieldDef->position > $maxFieldPos) {
                $maxFieldPos = $existingFieldDef->position;
            }
        }

        $fieldDefCreateStruct = new FieldDefinitionCreateStruct([
            'fieldTypeIdentifier' => $fieldTypeIdentifier,
            'identifier' => $this->resolveNewFieldDefinitionIdentifier(
                $contentTypeDraft,
                $maxFieldPos,
                $fieldTypeIdentifier
            ),
            'names' => [$event->getOption('languageCode') => 'New FieldDefinition'],
            'position' => $maxFieldPos + 1,
        ]);

        if (isset($this->groupsList)) {
            $fieldDefCreateStruct->fieldGroup = $this->groupsList->getDefaultGroup();
        }

        $this->contentTypeService->addFieldDefinition($contentTypeDraft, $fieldDefCreateStruct);
    }

    public function processRemoveFieldDefinition(FormActionEvent $event)
    {
        /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentTypeDraft $contentTypeDraft */
        $contentTypeDraft = $event->getData()->contentTypeDraft;

        // Accessing FieldDefinition user selection through the form and not the data,
        // as "selected" is not a property of FieldDefinitionData.
        /** @var \Symfony\Component\Form\FormInterface $fieldDefForm */
        foreach ($event->getForm()->get('fieldDefinitionsData') as $fieldDefForm) {
            if ($fieldDefForm->get('selected')->getData() === true) {
                $this->contentTypeService->removeFieldDefinition($contentTypeDraft, $fieldDefForm->getData()->fieldDefinition);
            }
        }
    }

    public function processPublishContentType(FormActionEvent $event)
    {
        $contentTypeDraft = $event->getData()->contentTypeDraft;
        $this->contentTypeService->publishContentTypeDraft($contentTypeDraft);
        if (isset($this->options['redirectRouteAfterPublish'])) {
            $event->setResponse(
                new RedirectResponse($this->router->generate($this->options['redirectRouteAfterPublish']))
            );
        }
    }

    public function processRemoveContentTypeDraft(FormActionEvent $event)
    {
        $contentTypeDraft = $event->getData()->contentTypeDraft;
        $this->contentTypeService->deleteContentType($contentTypeDraft);
        if (isset($this->options['redirectRouteAfterPublish'])) {
            $event->setResponse(
                new RedirectResponse($this->router->generate($this->options['redirectRouteAfterPublish']))
            );
        }
    }

    /**
     * Resolves unique field definition identifier.
     *
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentTypeDraft $contentTypeDraft
     * @param int $startIndex
     * @param string $fieldTypeIdentifier
     *
     * @return string
     */
    private function resolveNewFieldDefinitionIdentifier(
        ContentTypeDraft $contentTypeDraft,
        int $startIndex,
        string $fieldTypeIdentifier
    ): string {
        $fieldDefinitionIdentifiers = $contentTypeDraft
            ->getFieldDefinitions()
            ->map(static function (FieldDefinition $fieldDefinition): string {
                return $fieldDefinition->identifier;
            });

        do {
            $fieldDefinitionIdentifier = sprintf('new_%s_%d', $fieldTypeIdentifier, ++$startIndex);
        } while (in_array($fieldDefinitionIdentifier, $fieldDefinitionIdentifiers, true));

        return $fieldDefinitionIdentifier;
    }
}
