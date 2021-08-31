<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ContentType;

use eZ\Publish\API\Repository\ContentTypeService;
use EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader\ContentTypeChoiceLoader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form Type allowing to select ContentType.
 */
class ContentTypeChoiceType extends AbstractType
{
    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    protected $contentTypeService;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader\ContentTypeChoiceLoader */
    private $contentTypeChoiceLoader;

    /**
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader\ContentTypeChoiceLoader $contentTypeChoiceLoader
     */
    public function __construct(
        ContentTypeService $contentTypeService,
        ContentTypeChoiceLoader $contentTypeChoiceLoader
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->contentTypeChoiceLoader = $contentTypeChoiceLoader;
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'choice_loader' => $this->contentTypeChoiceLoader,
                'choice_label' => 'name',
                'choice_name' => 'identifier',
                'choice_value' => 'identifier',
            ]);
    }
}
