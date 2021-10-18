<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\URLManagement;

use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\URLWildcardService;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformAdminUi\Form\Data\URLWildcard\URLWildcardDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class URLWildcardsTab extends AbstractTab implements OrderedTabInterface
{
    public const URI_FRAGMENT = 'ibexa-tab-link-manager-url-wildcards';

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    protected $permissionResolver;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var \eZ\Publish\API\Repository\URLWildcardService */
    private $urlWildcardService;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    private $formFactory;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        PermissionResolver $permissionResolver,
        ConfigResolverInterface $configResolver,
        URLWildcardService $urlWildcardService,
        FormFactory $formFactory
    ) {
        parent::__construct($twig, $translator);

        $this->permissionResolver = $permissionResolver;
        $this->configResolver = $configResolver;
        $this->urlWildcardService = $urlWildcardService;
        $this->formFactory = $formFactory;
    }

    /**
     * @inheritdoc
     */
    public function getIdentifier(): string
    {
        return 'url-wildcards';
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return /** @Desc("URL wildcards") */
            $this->translator->trans('tab.name.url_wildcards', [], 'url_wildcard');
    }

    /**
     * @inheritdoc
     */
    public function getOrder(): int
    {
        return 20;
    }

    /**
     * @param array $parameters
     *
     * @return string
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function renderView(array $parameters): string
    {
        $urlWildcards = $this->urlWildcardService->loadAll();

        $urlWildcardsChoices = [];
        foreach ($urlWildcards as $urlWildcardItem) {
            $urlWildcardsChoices[$urlWildcardItem->id] = false;
        }

        $deleteUrlWildcardDeleteForm = $this->formFactory->deleteURLWildcard(
            new URLWildcardDeleteData($urlWildcardsChoices)
        );

        $addUrlWildcardForm = $this->formFactory->createURLWildcard();
        $urlWildcardsEnabled = $this->configResolver->getParameter('url_wildcards.enabled');
        $canManageWildcards = $this->permissionResolver->hasAccess('content', 'urltranslator');

        return $this->twig->render('@ezdesign/url_wildcard/list.html.twig', [
            'url_wildcards' => $urlWildcards,
            'form' => $deleteUrlWildcardDeleteForm->createView(),
            'form_add' => $addUrlWildcardForm->createView(),
            'url_wildcards_enabled' => $urlWildcardsEnabled,
            'can_manage' => $canManageWildcards,
        ]);
    }
}
