<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\LocationView;

use eZ\Publish\API\Repository\URLAliasService;
use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class UrlsTab extends AbstractTab implements OrderedTabInterface
{
    /** @var URLAliasService */
    protected $URLAliasService;

    /**
     * @param Environment $twig
     * @param TranslatorInterface $translator
     * @param URLAliasService $URLAliasService
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        URLAliasService $URLAliasService
    ) {
        parent::__construct($twig, $translator);

        $this->URLAliasService = $URLAliasService;
    }

    public function getIdentifier(): string
    {
        return 'urls_tab';
    }

    public function getName(): string
    {
        /** @Desc("URL") */
        return $this->translator->trans('tab.name.urls', [], 'locationview');
    }

    public function getOrder(): int
    {
        return 700;
    }

    public function renderView(array $parameters): string
    {
        /** @var Location $location */
        $location = $parameters['location'];

        $viewParameters = [
            'custom_urls' => $this->URLAliasService->listLocationAliases($location, true),
            'system_urls' => $this->URLAliasService->listLocationAliases($location, false),
        ];

        return $this->twig->render(
            '@ezdesign/content/tab/urls.html.twig',
            array_merge($viewParameters, $parameters)
        );
    }
}
