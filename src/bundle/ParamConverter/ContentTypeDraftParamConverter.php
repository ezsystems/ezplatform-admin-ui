<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\ParamConverter;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeDraft;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class ContentTypeDraftParamConverter implements ParamConverterInterface
{
    const PARAMETER_CONTENT_TYPE_ID = 'contentTypeId';

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /**
     * ContentTypeGroupParamConverter constructor.
     *
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeGroupService
     */
    public function __construct(ContentTypeService $contentTypeGroupService)
    {
        $this->contentTypeService = $contentTypeGroupService;
    }

    /**
     * @inheritdoc
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        if (!$request->get(self::PARAMETER_CONTENT_TYPE_ID)) {
            return false;
        }

        $id = (int)$request->get(self::PARAMETER_CONTENT_TYPE_ID);

        $contentTypeDraft = $this->contentTypeService->loadContentTypeDraft($id);

        $request->attributes->set($configuration->getName(), $contentTypeDraft);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function supports(ParamConverter $configuration)
    {
        return ContentTypeDraft::class === $configuration->getClass();
    }
}
