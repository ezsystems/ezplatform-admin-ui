<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Processor;

use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\Values\User\RoleDraft;
use EzSystems\EzPlatformAdminUi\Event\FormActionEvent;
use EzSystems\EzPlatformAdminUi\Event\RepositoryFormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PolicyFormProcessor implements EventSubscriberInterface
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
            RepositoryFormEvents::POLICY_UPDATE => 'processUpdatePolicy',
            RepositoryFormEvents::POLICY_SAVE => 'processSavePolicy',
            RepositoryFormEvents::POLICY_REMOVE_DRAFT => 'processRemoveDraft',
        ];
    }

    public function processUpdatePolicy(FormActionEvent $event)
    {
        // Don't update anything if we just want to cancel the draft.
        if ($event->getClickedButton() === 'removeDraft') {
            return;
        }

        /** @var \EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Role\PolicyCreateData|\EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Role\PolicyUpdateData $data */
        $data = $event->getData();
        if ($data->isNew() && $data->moduleFunction) {
            list($module, $function) = explode('|', $data->moduleFunction);
            $data->module = $module;
            $data->function = $function;
            $initialRoleDraft = $data->roleDraft;
            $updatedRoleDraft = $this->roleService->addPolicyByRoleDraft($initialRoleDraft, $data);

            $initialPoliciesById = $this->getPoliciesById($initialRoleDraft);
            $updatedPoliciesById = $this->getPoliciesById($updatedRoleDraft);
            foreach ($updatedPoliciesById as $policyId => $policyDraft) {
                if (!isset($initialPoliciesById[$policyId])) {
                    $data->setPolicyDraft($policyDraft);
                    break;
                }
            }
        } else {
            // Only save limitations on update.
            // It is not possible by design to update policy module/function.
            foreach ($data->limitationsData as $limitation) {
                // Add posted limitations as valid ones, recognized by RoleService.
                if (!empty($limitation->limitationValues)) {
                    $data->addLimitation($limitation);
                }
            }

            $this->roleService->updatePolicyByRoleDraft($data->roleDraft, $data->policyDraft, $data);
        }
    }

    /**
     * Returns policies for passed RoleDraft object, indexed by their IDs.
     *
     * @param RoleDraft $roleDraft
     *
     * @return array
     */
    private function getPoliciesById(RoleDraft $roleDraft)
    {
        $policies = [];
        foreach ($roleDraft->getPolicies() as $policy) {
            $policies[$policy->id] = $policy;
        }

        return $policies;
    }

    public function processSavePolicy(FormActionEvent $event)
    {
        /** @var \eZ\Publish\API\Repository\Values\User\RoleDraft $roleDraft */
        $roleDraft = $event->getData()->roleDraft;
        $this->roleService->publishRoleDraft($roleDraft);
    }

    public function processRemoveDraft(FormActionEvent $event)
    {
        /** @var \EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Role\PolicyCreateData|\EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Role\PolicyUpdateData $data */
        $data = $event->getData();
        if (!$data->isNew()) {
            $this->roleService->removePolicyByRoleDraft($data->roleDraft, $data->policyDraft);
        }
        $this->roleService->deleteRoleDraft($data->roleDraft);
    }
}
