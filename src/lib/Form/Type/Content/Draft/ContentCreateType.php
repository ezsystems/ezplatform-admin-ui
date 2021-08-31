<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Content\Draft;

use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\User\Limitation;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentCreateData;
use EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader\ContentCreateContentTypeChoiceLoader;
use EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader\ContentCreateLanguageChoiceLoader;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\LocationType;
use EzSystems\EzPlatformAdminUi\Form\Type\ContentType\ContentTypeChoiceType;
use EzSystems\EzPlatformAdminUi\Form\Type\Language\LanguageChoiceType;
use EzSystems\EzPlatformAdminUi\Permission\LookupLimitationsTransformer;
use EzSystems\EzPlatformAdminUi\Permission\PermissionCheckerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentCreateType extends AbstractType
{
    /** @var \eZ\Publish\API\Repository\LanguageService */
    protected $languageService;

    /** @var \Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface */
    private $contentTypeChoiceLoader;

    /** @var \Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface */
    private $languageChoiceLoader;

    /** @var \EzSystems\EzPlatformAdminUi\Permission\LookupLimitationsTransformer */
    private $lookupLimitationsTransformer;

    /** @var \EzSystems\EzPlatformAdminUi\Permission\PermissionCheckerInterface */
    private $permissionChecker;

    /**
     * @param \eZ\Publish\API\Repository\LanguageService $languageService
     * @param \Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface $contentTypeChoiceLoader
     * @param \Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface $languageChoiceLoader
     * @param \EzSystems\EzPlatformAdminUi\Permission\PermissionCheckerInterface $permissionChecker
     * @param \EzSystems\EzPlatformAdminUi\Permission\LookupLimitationsTransformer $lookupLimitationsTransformer
     */
    public function __construct(
        LanguageService $languageService,
        ChoiceLoaderInterface $contentTypeChoiceLoader,
        ChoiceLoaderInterface $languageChoiceLoader,
        PermissionCheckerInterface $permissionChecker,
        LookupLimitationsTransformer $lookupLimitationsTransformer
    ) {
        $this->languageService = $languageService;
        $this->contentTypeChoiceLoader = $contentTypeChoiceLoader;
        $this->languageChoiceLoader = $languageChoiceLoader;
        $this->permissionChecker = $permissionChecker;
        $this->lookupLimitationsTransformer = $lookupLimitationsTransformer;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $restrictedContentTypesIds = [];
        $restrictedLanguageCodes = [];

        /** @var \EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentCreateData $contentCreateData */
        $contentCreateData = $options['data'];
        if ($location = $contentCreateData->getParentLocation()) {
            $limitationsValues = $this->getLimitationValuesForLocation($location);
            $restrictedContentTypesIds = $limitationsValues[Limitation::CONTENTTYPE];
            $restrictedLanguageCodes = $limitationsValues[Limitation::LANGUAGE];
        }

        $builder
            ->add(
                'content_type',
                ContentTypeChoiceType::class,
                [
                    'label' => false,
                    'multiple' => false,
                    'expanded' => true,
                    'choice_loader' => new ContentCreateContentTypeChoiceLoader($this->contentTypeChoiceLoader, $restrictedContentTypesIds),
                ]
            )
            ->add(
                'parent_location',
                LocationType::class,
                ['label' => false]
            )
            ->add(
                'language',
                LanguageChoiceType::class,
                [
                    'label' => false,
                    'multiple' => false,
                    'expanded' => false,
                    'choice_loader' => new ContentCreateLanguageChoiceLoader($this->languageChoiceLoader, $restrictedLanguageCodes),
                ]
            )
            ->add(
                'create',
                SubmitType::class,
                [
                    'label' => /** @Desc("Create") */
                        'content_draft_create_type.create',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => ContentCreateData::class,
                'translation_domain' => 'forms',
            ]);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return array
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    private function getLimitationValuesForLocation(Location $location): array
    {
        $lookupLimitationsResult = $this->permissionChecker->getContentCreateLimitations($location);

        return $this->lookupLimitationsTransformer->getGroupedLimitationValues(
            $lookupLimitationsResult,
            [Limitation::CONTENTTYPE, Limitation::LANGUAGE]
        );
    }
}
