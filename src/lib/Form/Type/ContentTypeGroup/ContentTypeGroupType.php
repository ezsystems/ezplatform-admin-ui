<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ContentTypeGroup;

use eZ\Publish\API\Repository\ContentTypeService;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\ContentTypeGroupTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class ContentTypeGroupType extends AbstractType
{
    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    protected $contentTypeService;

    /**
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     */
    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new ContentTypeGroupTransformer($this->contentTypeService));
    }

    public function getParent()
    {
        return HiddenType::class;
    }
}
