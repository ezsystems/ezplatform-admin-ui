<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Action;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

interface UiActionEventInterface
{
    public const TYPE_SUCCESS = 'success';
    public const TYPE_FAILURE = 'failure';

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     */
    public function setName(string $name): void;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param string $type
     */
    public function setType(string $type): void;

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm(): FormInterface;

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     */
    public function setForm(FormInterface $form): void;

    /**
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function getResponse(): ?Response;

    /**
     * @param \Symfony\Component\HttpFoundation\Response|null $response
     */
    public function setResponse(?Response $response): void;
}
