<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\FieldType;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\Core\FieldType\ImageAsset\AssetMapper;
use EzSystems\EzPlatformContentForms\ConfigResolver\MaxUploadSize;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ImageAssetFieldType extends AbstractType
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\Core\FieldType\ImageAsset\AssetMapper */
    private $assetMapper;

    /** @var \EzSystems\EzPlatformContentForms\ConfigResolver\MaxUploadSize */
    private $maxUploadSize;

    /**
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\Core\FieldType\ImageAsset\AssetMapper $mapper
     * @param \EzSystems\EzPlatformContentForms\ConfigResolver\MaxUploadSize $maxUploadSize
     */
    public function __construct(ContentService $contentService, AssetMapper $mapper, MaxUploadSize $maxUploadSize)
    {
        $this->contentService = $contentService;
        $this->maxUploadSize = $maxUploadSize;
        $this->assetMapper = $mapper;
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezplatform_fieldtype_ezimageasset';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('destinationContentId', HiddenType::class)
            ->add(
                'remove',
                CheckboxType::class,
                [
                    'label' => /** @Desc("Remove") */ 'content.field_type.binary_base.remove',
                    'mapped' => false,
                ]
            )
            ->add(
                'file',
                FileType::class,
                [
                    'label' => /** @Desc("File") */ 'content.field_type.binary_base.file',
                    'required' => $options['required'],
                    'constraints' => [
                        new Assert\File([
                            'maxSize' => $this->getMaxFileSize(),
                        ]),
                    ],
                    'mapped' => false,
                ]
            )
            ->add(
                'alternativeText',
                TextType::class,
                [
                    'label' => /** @Desc("Alternative text") */ 'content.field_type.ezimageasset.alternative_text',
                ]
            );
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['destination_content'] = null;

        if ($view->vars['value']['destinationContentId']) {
            try {
                $content = $this->contentService->loadContent(
                    $view->vars['value']['destinationContentId']
                );

                if (!$content->contentInfo->isTrashed()) {
                    $view->vars['destination_content'] = $content;
                }
            } catch (NotFoundException | UnauthorizedException $exception) {
            }
        }

        $view->vars['max_file_size'] = $this->getMaxFileSize();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'ezrepoforms_fieldtype',
        ]);
    }

    /**
     * Returns max size of uploaded file in bytes.
     *
     * @return int
     */
    private function getMaxFileSize(): int
    {
        $validatorConfiguration = $this->assetMapper
            ->getAssetFieldDefinition()
            ->getValidatorConfiguration();

        $maxFileSize = $validatorConfiguration['FileSizeValidator']['maxFileSize'];
        if ($maxFileSize > 0) {
            return min($maxFileSize * 1024 * 1024, $this->maxUploadSize->get());
        }

        return $this->maxUploadSize->get();
    }
}
