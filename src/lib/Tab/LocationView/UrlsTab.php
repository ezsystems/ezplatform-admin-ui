<?php
declare(strict_types=1);

namespace EzPlatformAdminUi\Tab\LocationView;


use eZ\Publish\API\Repository\URLAliasService;
use eZ\Publish\API\Repository\Values\Content\Location;
use EzPlatformAdminUi\Tab\AbstractTab;
use EzPlatformAdminUi\Tab\OrderedTabInterface;
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
            'EzPlatformAdminUiBundle:content/tab:urls.html.twig',
            array_merge($viewParameters, $parameters)
        );
    }
}
