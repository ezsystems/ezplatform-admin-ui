<?php
declare(strict_types=1);

namespace EzPlatformAdminUi\Tab\LocationView;


use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Values\Content\Content;
use EzPlatformAdminUi\Tab\AbstractTab;
use EzPlatformAdminUi\Tab\OrderedTabInterface;
use EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class RelationsTab extends AbstractTab implements OrderedTabInterface
{
    /** @var PermissionResolver */
    protected $permissionResolver;

    /** @var DatasetFactory */
    protected $datasetFactory;

    /**
     * @param Environment $twig
     * @param TranslatorInterface $translator
     * @param PermissionResolver $permissionResolver
     * @param DatasetFactory $datasetFactory
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        PermissionResolver $permissionResolver,
        DatasetFactory $datasetFactory
    ) {
        parent::__construct($twig, $translator);

        $this->permissionResolver = $permissionResolver;
        $this->datasetFactory = $datasetFactory;
    }

    public function getIdentifier(): string
    {
        return 'relations';
    }

    public function getName(): string
    {
        /** @Desc("Relations") */
        return $this->translator->trans('tab.name.relations', [], 'locationview');
    }

    public function getOrder(): int
    {
        return 500;
    }

    public function renderView(array $parameters): string
    {
        /** @var Content $content */
        $content = $parameters['content'];
        $versionInfo = $content->getVersionInfo();
        $relationsDataset = $this->datasetFactory->relations();
        $relationsDataset->load($versionInfo);

        $viewParameters = ['relations' => $relationsDataset->getRelations()];

        if ($this->permissionResolver->hasAccess('module', 'reverserelatedlist') === true) {
            $viewParameters['reverse_relations'] = $relationsDataset->getReverseRelations();
        }

        return $this->twig->render(
            'EzPlatformAdminUiBundle:content/tab/relations:tab.html.twig',
            array_merge($viewParameters, $parameters)
        );
    }
}
