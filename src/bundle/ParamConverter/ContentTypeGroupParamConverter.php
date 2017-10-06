<?php

declare(strict_types=1);

namespace EzPlatformAdminUiBundle\ParamConverter;

use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup;
use EzPlatformAdminUi\Service\ContentTypeGroup\ContentTypeGroupService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContentTypeGroupParamConverter implements ParamConverterInterface
{
    const PARAMETER_CONTENT_TYPE_GROUP_ID = 'contentTypeGroupId';

    /**
     * @var ContentTypeGroupService
     */
    private $contentTypeGroupService;

    /**
     * ContentTypeGroupParamConverter constructor.
     *
     * @param ContentTypeGroupService $contentTypeGroupService
     */
    public function __construct(ContentTypeGroupService $contentTypeGroupService)
    {
        $this->contentTypeGroupService = $contentTypeGroupService;
    }

    /**
     * @inheritdoc
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $id = (int)$request->get(self::PARAMETER_CONTENT_TYPE_GROUP_ID);

        $group = $this->contentTypeGroupService->getContentTypeGroup($id);
        if (!$group) {
            throw new NotFoundHttpException("ContentTypeGroup $id not found!");
        }

        $request->attributes->set($configuration->getName(), $group);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function supports(ParamConverter $configuration)
    {
        return ContentTypeGroup::class === $configuration->getClass();
    }
}
