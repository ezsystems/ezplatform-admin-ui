<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ContentType;

use eZ\Publish\API\Repository\ContentTypeService;
use EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Provider\ContentTypeChoiceListProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form Type allowing to select ContentType.
 */
class ContentTypeChoiceType extends AbstractType
{
    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    protected $contentTypeService;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Provider\ContentTypeChoiceListProvider */
    private $contentTypeChoiceListProvider;

    /**
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Provider\ContentTypeChoiceListProvider $contentTypeChoiceListProvider
     */
    public function __construct(
        ContentTypeService $contentTypeService,
        ContentTypeChoiceListProvider $contentTypeChoiceListProvider
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->contentTypeChoiceListProvider = $contentTypeChoiceListProvider;
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'choice_loader' => new CallbackChoiceLoader([$this->contentTypeChoiceListProvider, 'getChoiceList']),
                'choice_label' => 'name',
                'choice_name' => 'identifier',
                'choice_value' => 'identifier',
            ]);
    }
}
