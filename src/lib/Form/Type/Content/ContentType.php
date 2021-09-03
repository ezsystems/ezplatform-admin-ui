<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Form\Type\Content;

use eZ\Publish\API\Repository\ContentService;
use Ibexa\AdminUi\Form\DataTransformer\ContentTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class ContentType extends AbstractType
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    protected $contentService;

    /**
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     */
    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new ContentTransformer($this->contentService));
    }

    /**
     * @inheritdoc
     */
    public function getParent(): ?string
    {
        return HiddenType::class;
    }
}

class_alias(ContentType::class, 'EzSystems\EzPlatformAdminUi\Form\Type\Content\ContentType');
