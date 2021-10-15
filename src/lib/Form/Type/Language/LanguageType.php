<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Form\Type\Language;

use eZ\Publish\API\Repository\LanguageService;
use Ibexa\AdminUi\Form\DataTransformer\LanguageTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class LanguageType extends AbstractType
{
    /** @var \eZ\Publish\API\Repository\LanguageService */
    protected $languageService;

    /**
     * @param \eZ\Publish\API\Repository\LanguageService $languageService
     */
    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new LanguageTransformer($this->languageService));
    }

    public function getParent()
    {
        return HiddenType::class;
    }
}

class_alias(LanguageType::class, 'EzSystems\EzPlatformAdminUi\Form\Type\Language\LanguageType');
