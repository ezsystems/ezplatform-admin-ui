<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\ContentType;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ContentTypeCreateType extends AbstractType
{
    /**
     * @var \eZ\Publish\API\Repository\ContentTypeService
     */
    private $contentTypeService;

    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezplatform_content_forms_contenttype_create';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'content_type',
            ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contentTypeGroupId', HiddenType::class, [
                'constraints' => new Callback(
                    function ($contentTypeGroupId, ExecutionContextInterface $context) {
                        try {
                            $this->contentTypeService->loadContentTypeGroup($contentTypeGroupId);
                        } catch (NotFoundException $e) {
                            $context
                                ->buildViolation('content_type.error.content_type_group.not_found')
                                ->setParameter('%id%', $contentTypeGroupId)
                                ->addViolation();
                        }
                    }
                ),
            ])
            ->add('create', SubmitType::class, ['label' => /** @Desc("Create a Content Type") */ 'content_type.create']);
    }
}
