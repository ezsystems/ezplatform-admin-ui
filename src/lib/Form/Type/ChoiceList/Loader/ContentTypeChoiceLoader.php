<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader;

use eZ\Publish\API\Repository\ContentTypeService;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

class ContentTypeChoiceLoader implements ChoiceLoaderInterface
{
    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    protected $contentTypeService;

    /**
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     */
    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoiceList(): array
    {
        $contentTypes = [];
        $contentTypeGroups = $this->contentTypeService->loadContentTypeGroups();
        foreach ($contentTypeGroups as $contentTypeGroup) {
            $contentTypes[$contentTypeGroup->identifier] = $this->contentTypeService->loadContentTypes($contentTypeGroup);
        }

        return $contentTypes;
    }

    /**
     * {@inheritdoc}
     */
    public function loadChoiceList($value = null)
    {
        $choices = $this->getChoiceList();

        return new ArrayChoiceList($choices, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function loadChoicesForValues(array $values, $value = null)
    {
        // Optimize
        $values = array_filter($values);
        if (empty($values)) {
            return [];
        }

        // If no callable is set, values are the same as choices
        if (null === $value) {
            return $values;
        }

        return $this->loadChoiceList($value)->getChoicesForValues($values);
    }

    /**
     * {@inheritdoc}
     */
    public function loadValuesForChoices(array $choices, $value = null)
    {
        // Optimize
        $choices = array_filter($choices);
        if (empty($choices)) {
            return [];
        }

        // If no callable is set, choices are the same as values
        if (null === $value) {
            return $choices;
        }

        return $this->loadChoiceList($value)->getValuesForChoices($choices);
    }
}
