<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\ParamConverter;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContentTypeGroupParamConverter implements ParamConverterInterface
{
    const PARAMETER_CONTENT_TYPE_GROUP_ID = 'contentTypeGroupId';

    /** @var ContentTypeService */
    private $contentTypeService;

    /**
     * ContentTypeGroupParamConverter constructor.
     *
     * @param ContentTypeService $contentTypeService
     */
    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        if (!$request->get(self::PARAMETER_CONTENT_TYPE_GROUP_ID)) {
            return false;
        }

        $id = (int)$request->get(self::PARAMETER_CONTENT_TYPE_GROUP_ID);

        $group = $this->contentTypeService->loadContentTypeGroup($id);
        if (!$group) {
            throw new NotFoundHttpException("ContentTypeGroup $id not found!");
        }

        $request->attributes->set($configuration->getName(), $group);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        return ContentTypeGroup::class === $configuration->getClass();
    }
}
