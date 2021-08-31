<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\ParamConverter;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContentTypeParamConverter implements ParamConverterInterface
{
    const PARAMETER_CONTENT_TYPE_ID = 'contentTypeId';
    const PARAMETER_CONTENT_TYPE_IDENTIFIER = 'contentTypeIdentifier';

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface */
    private $languagePreferenceProvider;

    /**
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeGroupService
     * @param \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface $languagePreferenceProvider
     */
    public function __construct(
        ContentTypeService $contentTypeGroupService,
        UserLanguagePreferenceProviderInterface $languagePreferenceProvider
    ) {
        $this->contentTypeService = $contentTypeGroupService;
        $this->languagePreferenceProvider = $languagePreferenceProvider;
    }

    /**
     * @inheritdoc
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        if (!$request->get(self::PARAMETER_CONTENT_TYPE_ID) && !$request->get(self::PARAMETER_CONTENT_TYPE_IDENTIFIER)) {
            return false;
        }

        $prioritizedLanguages = $this->languagePreferenceProvider->getPreferredLanguages();

        try {
            if ($request->get(self::PARAMETER_CONTENT_TYPE_ID)) {
                $id = (int)$request->get(self::PARAMETER_CONTENT_TYPE_ID);
                $contentType = $this->contentTypeService->loadContentType($id, $prioritizedLanguages);
            } elseif ($request->get(self::PARAMETER_CONTENT_TYPE_IDENTIFIER)) {
                $identifier = $request->get(self::PARAMETER_CONTENT_TYPE_IDENTIFIER);
                $contentType = $this->contentTypeService->loadContentTypeByIdentifier($identifier, $prioritizedLanguages);
            }
        } catch (NotFoundException $e) {
            throw new NotFoundHttpException('Content Type ' . ($id ?? $identifier) . ' not found.');
        }

        $request->attributes->set($configuration->getName(), $contentType);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function supports(ParamConverter $configuration)
    {
        return ContentType::class === $configuration->getClass();
    }
}
