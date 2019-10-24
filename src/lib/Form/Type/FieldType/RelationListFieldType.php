<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\FieldType;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\FieldType\RelationList\Value;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\FieldType\RelationListValueTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form Type representing ezobjectrelationlist field type.
 */
class RelationListFieldType extends AbstractType
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
        return 'ezplatform_fieldtype_ezobjectrelationlist';
    }

    public function getParent()
    {
        return TextType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new RelationListValueTransformer());
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['relations'] = [];
        $view->vars['default_location'] = $options['default_location'];

        /** @var Value $data */
        $data = $form->getData();

        if (!$data instanceof Value) {
            return;
        }

        foreach ($data->destinationContentIds as $contentId) {
            $contentInfo = $this->contentService->loadContentInfo($contentId);
            $contentType = $this->contentTypeService->loadContentType($contentInfo->contentTypeId);

            $view->vars['relations'][$contentId] = [
                'contentInfo' => $contentInfo,
                'contentType' => $contentType,
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'default_location' => null,
        ]);

        $resolver->setAllowedTypes('default_location', ['null', Location::class]);
    }
}
