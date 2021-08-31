<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\ParamConverter;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContentInfoParamConverter implements ParamConverterInterface
{
    const PARAMETER_CONTENT_INFO_ID = 'contentInfoId';

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /**
     * @param \eZ\Publish\API\Repository\ContentService $contentTypeService
     */
    public function __construct(ContentService $contentTypeService)
    {
        $this->contentService = $contentTypeService;
    }

    /**
     * @inheritdoc
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $id = (int)$request->get(self::PARAMETER_CONTENT_INFO_ID);
        $contentInfo = $this->contentService->loadContentInfo($id);

        if (!$contentInfo) {
            throw new NotFoundHttpException("Content Info $id not found.");
        }

        $request->attributes->set($configuration->getName(), $contentInfo);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function supports(ParamConverter $configuration)
    {
        return ContentInfo::class === $configuration->getClass();
    }
}
