<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields;

use Behat\Mink\Element\NodeElement;
use EzSystems\Behat\Browser\Context\BrowserContext;
use EzSystems\Behat\Browser\Element\Element;
use PHPUnit\Framework\Assert;

class DefaultFieldElement extends Element
{
    public const ELEMENT_NAME = 'DefaultFieldElement';

    /** @var string */
    private $fieldName;

    /** @var string */
    private $containerSelector;

    public function __construct(BrowserContext $context, string $fieldName, string $containerSelector)
    {
        parent::__construct($context);
        $this->fieldName = $fieldName;
        $this->containerSelector = $containerSelector;
    }

    public function setValue(string $value): void
    {
        $field = $this->findField();
        switch ($field->getAttribute('type')) {
            case 'text':
            case 'email':
                $this->context->waitUntil($this->defaultTimeout, function () use ($field, $value) {
                    $field->setValue($value);

                    return $this->getValue() === $value;
                });
                break;
            case 'checkbox':
                $field->setValue(filter_var($value, FILTER_VALIDATE_BOOLEAN));
                break;
            case 'radio':
                if ($field->isChecked() !== filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
                    $field->click();
                }
                break;
            default:
                throw new \Exception(sprintf('Field type "%s" not defined as Behat field element.', $field->getAttribute('type')));
        }
    }

    public function getValue(): string
    {
        $field = $this->findField();

        switch ($field->getAttribute('type')) {
            case 'text':
            case 'email':
            case 'checkbox':
                return $field->getValue();
            case 'radio':
                return $field->isChecked() ? 'true' : 'false';
            default:
                throw new \Exception(sprintf('Field type "%s" not defined as Behat field element.', $field->getAttribute('type')));
        }
    }

    private function findField(): NodeElement
    {
        $containerNode = $this->context->findElement($this->containerSelector);
        $field = $containerNode->findField($this->fieldName);
        Assert::assertNotNull($field, sprintf('Field %s not found.', $this->fieldName));

        return $field;
    }
}
