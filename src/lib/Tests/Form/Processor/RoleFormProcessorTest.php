<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\EzPlatformAdminUi\Tests\Form\Processor;

use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\Core\Repository\Values\User\RoleDraft;
use EzSystems\EzPlatformAdminUi\Event\FormActionEvent;
use EzSystems\EzPlatformAdminUi\Event\RepositoryFormEvents;
use EzSystems\EzPlatformAdminUi\Form\Processor\RoleFormProcessor;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Role\RoleData;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\RouterInterface;

class RoleFormProcessorTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $roleService;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $router;

    /**
     * @var RoleFormProcessor
     */
    private $formProcessor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->roleService = $this->createMock(RoleService::class);
        $this->router = $this->createMock(RouterInterface::class);
        $this->formProcessor = new RoleFormProcessor($this->roleService, $this->router);
    }

    public function testSubscribedEvents()
    {
        self::assertSame([
            RepositoryFormEvents::ROLE_UPDATE => ['processDefaultAction'],
            RepositoryFormEvents::ROLE_SAVE => ['processSaveRole'],
            RepositoryFormEvents::ROLE_REMOVE_DRAFT => ['processRemoveDraft'],
        ], RoleFormProcessor::getSubscribedEvents());
    }

    public function testProcessDefaultAction()
    {
        $roleDraft = new RoleDraft();
        $roleData = new RoleData(['roleDraft' => $roleDraft]);
        $event = new FormActionEvent($this->createMock(FormInterface::class), $roleData, null);

        $this->roleService
            ->expects($this->at(0))
            ->method('updateRoleDraft')
            ->with($roleDraft, $roleData);
        $this->roleService
            ->expects($this->at(1))
            ->method('publishRoleDraft')
            ->with($roleDraft);

        $this->formProcessor->processDefaultAction($event);
    }

    public function testProcessSaveRole()
    {
        $roleDraft = new RoleDraft();
        $roleData = new RoleData(['roleDraft' => $roleDraft]);
        $event = new FormActionEvent($this->createMock(FormInterface::class), $roleData, 'saveRole');

        $this->roleService
            ->expects($this->at(0))
            ->method('updateRoleDraft')
            ->with($roleDraft, $roleData);
        $this->roleService
            ->expects($this->at(1))
            ->method('publishRoleDraft')
            ->with($roleDraft);

        $this->formProcessor->processSaveRole($event);
    }

    public function testProcessRemoveDraft()
    {
        $roleDraft = new RoleDraft();
        $roleData = new RoleData(['roleDraft' => $roleDraft]);
        $event = new FormActionEvent($this->createMock(FormInterface::class), $roleData, 'removeDraft');

        $this->roleService
            ->expects($this->once())
            ->method('deleteRoleDraft')
            ->with($roleDraft);

        $this->formProcessor->processRemoveDraft($event);
    }
}
