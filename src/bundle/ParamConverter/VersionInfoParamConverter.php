<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\ParamConverter;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class VersionInfoParamConverter implements ParamConverterInterface
{
    const PARAMETER_VERSION_NO = 'versionNo';
    const PARAMETER_CONTENT_ID = 'contentId';

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
        if (!$request->get(self::PARAMETER_VERSION_NO) || !$request->get(self::PARAMETER_CONTENT_ID)) {
            return false;
        }

        $versionNo = (int)$request->get(self::PARAMETER_VERSION_NO);
        $contentId = (int)$request->get(self::PARAMETER_CONTENT_ID);

        $contentInfo = $this->contentService->loadContentInfo($contentId);
        $versionInfo = $this->contentService->loadVersionInfo($contentInfo, $versionNo);

        $request->attributes->set($configuration->getName(), $versionInfo);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function supports(ParamConverter $configuration): bool
    {
        return VersionInfo::class === $configuration->getClass();
    }
}
