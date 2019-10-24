<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\FieldType;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\FieldType\Relation\Value;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\FieldType\RelationValueTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form Type representing ezobjectrelation field type.
 */
class RelationFieldType extends AbstractType
{
    /** @var ContentService */
    private $contentService;

    /** @var ContentTypeService */
    private $contentTypeService;

    /**
     * @param ContentService $contentService
     * @param ContentTypeService $contentTypeService
     */
    public function __construct(ContentService $contentService, ContentTypeService $contentTypeService)
    {
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezplatform_fieldtype_ezobjectrelation';
    }

    public function getParent()
    {
        return IntegerType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new RelationValueTransformer());
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['relations'] = [];
        $view->vars['default_location'] = $options['default_location'];

        /** @var Value $data */
        $data = $form->getData();

        if (!$data instanceof Value || null === $data->destinationContentId) {
            return;
        }

        $contentInfo = $this->contentService->loadContentInfo($data->destinationContentId);
        $contentType = $this->contentTypeService->loadContentType($contentInfo->contentTypeId);

        $view->vars['relations'][$data->destinationContentId] = [
            'contentInfo' => $contentInfo,
            'contentType' => $contentType,
        ];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' => [
                'min' => 1,
                'step' => 1,
            ],
            'default_location' => null,
        ]);

        $resolver->setAllowedTypes('default_location', ['null', Location::class]);
    }
}
