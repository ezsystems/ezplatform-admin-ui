<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Action;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

class UiActionEvent extends Event implements UiActionEventInterface
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $type;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var \Symfony\Component\HttpFoundation\Response|null */
    protected $response;

    /**
     * @param string $name
     * @param string $type
     * @param \Symfony\Component\Form\FormInterface $form
     * @param \Symfony\Component\HttpFoundation\Response|null $response
     */
    public function __construct(string $name, string $type, FormInterface $form, ?Response $response)
    {
        $this->name = $name;
        $this->type = $type;
        $this->form = $form;
        $this->response = $response;
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @inheritdoc
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @inheritdoc
     */
    public function getForm(): FormInterface
    {
        return $this->form;
    }

    /**
     * @inheritdoc
     */
    public function setForm(FormInterface $form): void
    {
        $this->form = $form;
    }

    /**
     * @inheritdoc
     */
    public function getResponse(): ?Response
    {
        return $this->response;
    }

    /**
     * @inheritdoc
     */
    public function setResponse(?Response $response): void
    {
        $this->response = $response;
    }
}
