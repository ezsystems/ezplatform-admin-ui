<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\UI\Config\Provider;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface;
use EzSystems\EzPlatformAdminUi\UI\Config\ProviderInterface;
use EzSystems\EzPlatformAdminUi\UI\Service\ContentTypeIconResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ContentTypes implements ProviderInterface
{
    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface */
    private $userLanguagePreferenceProvider;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Service\ContentTypeIconResolver */
    private $contentTypeIconResolver;

    /** @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface */
    private $urlGenerator;

    /**
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface $userLanguagePreferenceProvider
     * @param \EzSystems\EzPlatformAdminUi\UI\Service\ContentTypeIconResolver $contentTypeIconResolver
     * @param \Symfony\Component\Routing\Generator\UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        ContentTypeService $contentTypeService,
        UserLanguagePreferenceProviderInterface $userLanguagePreferenceProvider,
        ContentTypeIconResolver $contentTypeIconResolver,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->userLanguagePreferenceProvider = $userLanguagePreferenceProvider;
        $this->contentTypeIconResolver = $contentTypeIconResolver;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @return mixed Anything that is serializable via json_encode()
     */
    public function getConfig()
    {
        $contentTypeGroups = [];

        $preferredLanguages = $this->userLanguagePreferenceProvider->getPreferredLanguages();
        $loadedContentTypeGroups = $this->contentTypeService->loadContentTypeGroups(
            $preferredLanguages
        );
        foreach ($loadedContentTypeGroups as $contentTypeGroup) {
            $contentTypes = $this->contentTypeService->loadContentTypes(
                $contentTypeGroup,
                $preferredLanguages
            );

            usort($contentTypes, static function (ContentType $contentType1, ContentType $contentType2) {
                return strnatcasecmp($contentType1->getName(), $contentType2->getName());
            });

            foreach ($contentTypes as $contentType) {
                $contentTypeGroups[$contentTypeGroup->identifier][] = [
                    'id' => $contentType->id,
                    'identifier' => $contentType->identifier,
                    'name' => $contentType->getName(),
                    'isContainer' => $contentType->isContainer,
                    'thumbnail' => $this->contentTypeIconResolver->getContentTypeIcon($contentType->identifier),
                    'href' => $this->urlGenerator->generate('ezpublish_rest_loadContentType', [
                        'contentTypeId' => $contentType->id,
                    ]),
                ];
            }
        }

        return $contentTypeGroups;
    }
}
