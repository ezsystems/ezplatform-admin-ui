<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Processor;

use eZ\Publish\API\Repository\RoleService;
use EzSystems\EzPlatformAdminUi\Event\FormActionEvent;
use EzSystems\EzPlatformAdminUi\Event\RepositoryFormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RoleFormProcessor implements EventSubscriberInterface
{
    /**
     * @var RoleService
     */
    private $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public static function getSubscribedEvents()
    {
        return [
            RepositoryFormEvents::ROLE_UPDATE => ['processDefaultAction'],
            RepositoryFormEvents::ROLE_SAVE => ['processSaveRole'],
            RepositoryFormEvents::ROLE_REMOVE_DRAFT => ['processRemoveDraft'],
        ];
    }

    public function processDefaultAction(FormActionEvent $event)
    {
        if ($this->isDefaultEvent($event)) {
            $this->processSaveRole($event);
        }
    }

    public function processSaveRole(FormActionEvent $event)
    {
        $roleDraft = $this->getRoleDraft($event);

        $this->roleService->updateRoleDraft($roleDraft, $this->getRoleData($event));
        $this->roleService->publishRoleDraft($roleDraft);
    }

    public function processRemoveDraft(FormActionEvent $event)
    {
        $this->roleService->deleteRoleDraft($this->getRoleDraft($event));
    }

    /**
     * Returns true if the event is the default event, meaning Enter has been pressed in the form.
     *
     * There's no need to process the default action (save) for explicit events (save, cancel).
     * Saving is not needed when cancelling, and when the Save button is clicked the save action takes care of it.
     * The default action is only needed when the form has been submitted by pressing Enter.
     *
     * @param FormActionEvent $event
     *
     * @return bool
     */
    protected function isDefaultEvent(FormActionEvent $event)
    {
        return $event->getClickedButton() === null;
    }

    /**
     * Returns the role data for the event.
     *
     * @param FormActionEvent $event
     *
     * @return \EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Role\RoleData
     */
    protected function getRoleData(FormActionEvent $event)
    {
        return $event->getData();
    }

    /**
     * Returns the role draft for the event.
     *
     * @param FormActionEvent $event
     *
     * @return \eZ\Publish\API\Repository\Values\User\RoleDraft
     */
    protected function getRoleDraft(FormActionEvent $event)
    {
        return $this->getRoleData($event)->roleDraft;
    }
}
