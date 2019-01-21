<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\UI\Config\Provider;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface;
use EzSystems\EzPlatformAdminUi\UI\Config\ProviderInterface;

/**
 * @deprecated please move to use ContentTypes so we don't have to load the same data several times
 */
class ContentTypeNames implements ProviderInterface
{
    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

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
     * @return mixed Anything that is serializable via json_encode()
     */
    public function getConfig()
    {
        $contentTypeNames = [];

        $preferredLanguages = $this->userLanguagePreferenceProvider->getPreferredLanguages();
        $contentTypeGroups = $this->contentTypeService->loadContentTypeGroups($preferredLanguages);
        foreach ($contentTypeGroups as $contentTypeGroup) {
            $contentTypes = $this->contentTypeService->loadContentTypes(
                $contentTypeGroup,
                $preferredLanguages
            );
            foreach ($contentTypes as $contentType) {
                $contentTypeNames[$contentType->identifier] = $contentType->getName();
            }
        }

        return $contentTypeNames;
    }
}
