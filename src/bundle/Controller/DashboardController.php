<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\PermissionResolver;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentEditData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller
{
    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    protected $formFactory;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory $formFactory
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     */
    public function __construct(
        FormFactory $formFactory,
        PermissionResolver $permissionResolver
    ) {
        $this->formFactory = $formFactory;
        $this->permissionResolver = $permissionResolver;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function dashboardAction(): Response
    {
        $editForm = $this->formFactory->contentEdit(
            new ContentEditData()
        );

        return $this->render('@ezdesign/ui/dashboard/dashboard.html.twig', [
            'form_edit' => $editForm->createView(),
            'can_create_content' => $this->permissionResolver->hasAccess('content', 'create'),
        ]);
    }
}
