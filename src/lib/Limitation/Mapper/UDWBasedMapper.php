<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Limitation\Mapper;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Subtree;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\Location\Path;
use eZ\Publish\API\Repository\Values\User\Limitation;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\UDWBasedValueModelTransformer;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\UDWBasedValueViewTransformer;
use EzSystems\EzPlatformAdminUi\Limitation\LimitationFormMapperInterface;
use EzSystems\EzPlatformAdminUi\Limitation\LimitationValueMapperInterface;
use EzSystems\EzPlatformAdminUi\Translation\Extractor\LimitationTranslationExtractor;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;

/**
 * Base class for mappers based on Universal Discovery Widget.
 */
class UDWBasedMapper implements LimitationFormMapperInterface, LimitationValueMapperInterface
{
    /**
     * @var \eZ\Publish\API\Repository\LocationService
     */
    protected $locationService;

    /**
     * @var \eZ\Publish\API\Repository\SearchService
     */
    protected $searchService;

    /**
     * Form template to use.
     *
     * @var string
     */
    private $template;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    public function __construct(
        LocationService $locationService,
        SearchService $searchService,
        PermissionResolver $permissionResolver,
        Repository $repository
    ) {
        $this->locationService = $locationService;
        $this->searchService = $searchService;
        $this->permissionResolver = $permissionResolver;
        $this->repository = $repository;
    }

    public function setFormTemplate($template)
    {
        $this->template = $template;
    }

    public function getFormTemplate()
    {
        return $this->template;
    }

    public function mapLimitationForm(FormInterface $form, Limitation $data)
    {
        $form->add(
            // Creating from FormBuilder as we need to add a DataTransformer.
            $form->getConfig()->getFormFactory()
                ->createBuilder()
                ->create('limitationValues', HiddenType::class, [
                    'required' => false,
                    'label' => LimitationTranslationExtractor::identifierToLabel($data->getIdentifier()),
                ])
                ->addViewTransformer(new UDWBasedValueViewTransformer($this->locationService))
                ->addModelTransformer(
                    new UDWBasedValueModelTransformer(
                        $this->locationService,
                        $this->permissionResolver,
                        $this->repository
                    )
                )
                // Deactivate auto-initialize as we're not on the root form.
                ->setAutoInitialize(false)->getForm()
        );
    }

    public function filterLimitationValues(Limitation $limitation)
    {
    }

    public function mapLimitationValue(Limitation $limitation)
    {
        $values = [];

        foreach ($limitation->limitationValues as $id) {
            $location = $this->locationService->loadLocation($id);

            $query = new LocationQuery([
                'filter' => new Subtree($location->pathString),
                'sortClauses' => [new Path()],
            ]);

            $path = [];
            foreach ($this->searchService->findLocations($query)->searchHits as $hit) {
                $path[] = $hit->valueObject->getContentInfo();
            }

            $values[] = $path;
        }

        return $values;
    }
}
