<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Factory;

use EzSystems\EzPlatformAdminUi\Form\Data\ContentType\ContentTypeEditData;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentType\Translation\TranslationAddData;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentType\Translation\TranslationRemoveData;
use EzSystems\EzPlatformAdminUi\Form\Type\ContentType\Translation\TranslationAddType;
use EzSystems\EzPlatformAdminUi\Form\Type\ContentType\ContentTypeEditType;
use EzSystems\EzPlatformAdminUi\Form\Type\ContentType\Translation\TranslationRemoveType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Util\StringUtil;

class ContentTypeFormFactory
{
    /** @var \Symfony\Component\Form\FormFactoryInterface */
    private $formFactory;

    /**
     * @param \Symfony\Component\Form\FormFactoryInterface $formFactory
     */
    public function __construct(
        FormFactoryInterface $formFactory
    ) {
        $this->formFactory = $formFactory;
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\ContentType\Translation\TranslationAddData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function addContentTypeTranslation(
        TranslationAddData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: sprintf('add-translation');

        return $this->formFactory->createNamed($name, TranslationAddType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\ContentType\Translation\TranslationRemoveData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function removeContentTypeTranslation(
        TranslationRemoveData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: sprintf('delete-translations');

        return $this->formFactory->createNamed($name, TranslationRemoveType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\ContentType\ContentTypeEditData|null $data
     * @param string|null $name
     * @param array $options
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function contentTypeEdit(
        ContentTypeEditData $data = null,
        ?string $name = null,
        array $options = []
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(ContentTypeEditType::class);

        return $this->formFactory->createNamed($name, ContentTypeEditType::class, $data, $options);
    }
}
