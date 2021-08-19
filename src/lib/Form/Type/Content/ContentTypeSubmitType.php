<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Form\Type\Content;

use eZ\Publish\API\Repository\ContentTypeService;
use Ibexa\AdminUi\Form\DataTransformer\ContentTypeTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ContentTypeSubmitType extends AbstractType
{
    /** @var ContentTypeService */
    protected $contentTypeService;

    /**
     * @param ContentTypeService $contentTypeService
     */
    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new ContentTypeTransformer($this->contentTypeService));
    }

    public function getParent()
    {
        return SubmitType::class;
    }
}

class_alias(ContentTypeSubmitType::class, 'EzSystems\EzPlatformAdminUi\Form\Type\Content\ContentTypeSubmitType');
