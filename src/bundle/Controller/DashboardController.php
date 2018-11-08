<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentEditData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use eZ\Publish\API\Repository\PermissionResolver;

class DashboardController extends Controller
{
    protected $formFactory;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /**
     * @param \EzSystems\EzplatformAdminUi\Form\Factory\FormFactory $formFactory
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     */
    public function __construct(FormFactory $formFactory, PermissionResolver $permissionResolver)
    {
        $this->formFactory = $formFactory;
        $this->permissionResolver = $permissionResolver;
    }

    public function dashboardAction()
    {
        $editForm = $this->formFactory->contentEdit(
            new ContentEditData()
        );

        return $this->render('@ezdesign/dashboard/dashboard.html.twig', [
            'form_edit' => $editForm->createView(),
            'can_create_content' => $this->permissionResolver->hasAccess('content', 'create'),
        ]);
    }
}
