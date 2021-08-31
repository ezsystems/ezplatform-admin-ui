<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

class ContentTypeChoiceLoader implements ChoiceLoaderInterface
{
    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    protected $contentTypeService;

    /** @var \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface */
    private $userLanguagePreferenceProvider;

    /**
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface $userLanguagePreferenceProvider
     */
    public function __construct(ContentTypeService $contentTypeService, UserLanguagePreferenceProviderInterface $userLanguagePreferenceProvider)
    {
        $this->contentTypeService = $contentTypeService;
        $this->userLanguagePreferenceProvider = $userLanguagePreferenceProvider;
    }

    /**
     * @inheritdoc
     */
    public function getChoiceList(): array
    {
        $contentTypesList = [];
        $preferredLanguages = $this->userLanguagePreferenceProvider->getPreferredLanguages();
        $contentTypeGroups = $this->contentTypeService->loadContentTypeGroups($preferredLanguages);
        foreach ($contentTypeGroups as $contentTypeGroup) {
            $contentTypes = $this->contentTypeService->loadContentTypes($contentTypeGroup, $preferredLanguages);
            usort($contentTypes, static function (ContentType $contentType1, ContentType $contentType2) {
                return strnatcasecmp($contentType1->getName(), $contentType2->getName());
            });

            $contentTypesList[$contentTypeGroup->identifier] = $contentTypes;
        }

        return $contentTypesList;
    }

    /**
     * @inheritdoc
     */
    public function loadChoiceList($value = null)
    {
        $choices = $this->getChoiceList();

        return new ArrayChoiceList($choices, $value);
    }

    /**
     * @inheritdoc
     */
    public function loadChoicesForValues(array $values, $value = null)
    {
        // Optimize
        $values = array_filter($values);
        if (empty($values)) {
            return [];
        }

        return $this->loadChoiceList($value)->getChoicesForValues($values);
    }

    /**
     * @inheritdoc
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
