<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\ParamConverter;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\Content;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Loads Content object using ids from request parameters.
 */
class ContentParamConverter implements ParamConverterInterface
{
    const PARAMETER_CONTENT_ID = 'contentId';
    const PARAMETER_VERSION_NO = 'versionNo';
    const PARAMETER_LANGUAGE_CODE = 'languageCode';

    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    private $contentService;

    /**
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     */
    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * @inheritdoc
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $contentId = $request->get(self::PARAMETER_CONTENT_ID);
        $versionNo = $request->get(self::PARAMETER_VERSION_NO);
        $languageCode = $request->get(self::PARAMETER_LANGUAGE_CODE);

        if (null === $contentId || !\is_array($languageCode)) {
            return false;
        }

        $content = $this->contentService->loadContent($contentId, $languageCode, $versionNo);

        $request->attributes->set($configuration->getName(), $content);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function supports(ParamConverter $configuration): bool
    {
        return Content::class === $configuration->getClass();
    }
}
