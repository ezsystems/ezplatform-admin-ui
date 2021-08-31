<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Factory;

use EzSystems\EzPlatformAdminUi\Form\Data\Search\TrashSearchData;
use EzSystems\EzPlatformAdminUi\Form\Data\Trash\TrashEmptyData;
use EzSystems\EzPlatformAdminUi\Form\Data\Trash\TrashItemDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Trash\TrashItemRestoreData;
use EzSystems\EzPlatformAdminUi\Form\Type\Search\TrashSearchType;
use EzSystems\EzPlatformAdminUi\Form\Type\Trash\TrashEmptyType;
use EzSystems\EzPlatformAdminUi\Form\Type\Trash\TrashItemDeleteType;
use EzSystems\EzPlatformAdminUi\Form\Type\Trash\TrashItemRestoreType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Util\StringUtil;

class TrashFormFactory
{
    /** @var \Symfony\Component\Form\FormFactoryInterface */
    private $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function restoreTrashItem(
        ?TrashItemRestoreData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(TrashItemRestoreType::class);

        return $this->formFactory->createNamed($name, TrashItemRestoreType::class, $data);
    }

    /**
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function deleteTrashItem(
        TrashItemDeleteData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(TrashItemDeleteType::class);

        return $this->formFactory->createNamed($name, TrashItemDeleteType::class, $data);
    }

    public function emptyTrash(
        ?TrashEmptyData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(TrashEmptyType::class);

        return $this->formFactory->createNamed($name, TrashEmptyType::class, $data);
    }

    public function searchTrash(
        ?TrashSearchData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(TrashSearchType::class);

        return $this->formFactory->createNamed($name, TrashSearchType::class, $data);
    }
}
