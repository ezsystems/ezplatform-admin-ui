<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Event;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

class FormActionEvent extends FormEvent
{
    /**
     * Name of the button used to submit the form.
     *
     * @var string|null
     */
    private $clickedButton;

    /**
     * Hash of options.
     *
     * @var array
     */
    private $options;

    /**
     * Response to return after form post-processing. Typically a RedirectResponse.
     *
     * @var \Symfony\Component\HttpFoundation\Response|null
     */
    private $response;

    /**
     * Additional payload populated for event listeners next in priority.
     *
     * @var array
     */
    private $payloads;

    public function __construct(
        FormInterface $form,
        $data,
        ?string $clickedButton,
        array $options = [],
        array $payloads = []
    ) {
        parent::__construct($form, $data);
        $this->clickedButton = $clickedButton;
        $this->options = $options;
        $this->payloads = $payloads;
    }

    public function getClickedButton(): ?string
    {
        return $this->clickedButton;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param string $optionName The option name
     * @param mixed $defaultValue default value to return if option is not set
     *
     * @return mixed
     */
    public function getOption($optionName, $defaultValue = null)
    {
        if (!isset($this->options[$optionName])) {
            return $defaultValue;
        }

        return $this->options[$optionName];
    }

    public function hasOption(string $optionName): bool
    {
        return isset($this->options[$optionName]);
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }

    public function hasResponse(): bool
    {
        return $this->response !== null;
    }

    public function getPayloads(): array
    {
        return $this->payloads;
    }

    public function setPayloads(array $payloads): void
    {
        $this->payloads = $payloads;
    }

    public function hasPayload(string $name): bool
    {
        return isset($this->payloads[$name]);
    }

    public function getPayload(string $name)
    {
        return $this->payloads[$name];
    }

    public function setPayload(string $name, $payload): void
    {
        $this->payloads[$name] = $payload;
    }
}
