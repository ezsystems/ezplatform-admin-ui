<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Action;

use Symfony\Component\Form\FormInterface;
use Traversable;

class FormUiActionMappingDispatcher
{
    /** @var \EzSystems\EzPlatformAdminUi\UI\Action\FormUiActionMapperInterface[] */
    protected $mappers;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Action\FormUiActionMapperInterface */
    protected $defaultMapper;

    /**
     * @param \Traversable $mappers
     * @param \EzSystems\EzPlatformAdminUi\UI\Action\FormUiActionMapperInterface $defaultMapper
     */
    public function __construct(
        Traversable $mappers,
        FormUiActionMapperInterface $defaultMapper
    ) {
        $this->mappers = $mappers;
        $this->defaultMapper = $defaultMapper;
    }

    /**
     * @return \EzSystems\EzPlatformAdminUi\UI\Action\FormUiActionMapperInterface[]
     */
    public function getMappers(): array
    {
        return $this->mappers;
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\UI\Action\FormUiActionMapperInterface[] $mappers
     */
    public function setMappers(array $mappers): void
    {
        $this->mappers = $mappers;
    }

    /**
     * @return \EzSystems\EzPlatformAdminUi\UI\Action\FormUiActionMapperInterface
     */
    public function getDefaultMapper(): FormUiActionMapperInterface
    {
        return $this->defaultMapper;
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\UI\Action\FormUiActionMapperInterface $defaultMapper
     */
    public function setDefaultMapper(FormUiActionMapperInterface $defaultMapper): void
    {
        $this->defaultMapper = $defaultMapper;
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     *
     * @return \EzSystems\EzPlatformAdminUi\UI\Action\UiActionEvent
     */
    public function dispatch(FormInterface $form): UiActionEvent
    {
        /** @var \EzSystems\EzPlatformAdminUi\UI\Action\FormUiActionMapperInterface[] $mappers */
        foreach ($this->mappers as $mapper) {
            if ($mapper === $this->defaultMapper) {
                continue;
            }

            if ($mapper->supports($form)) {
                return $mapper->map($form);
            }
        }

        return $this->defaultMapper->map($form);
    }
}
