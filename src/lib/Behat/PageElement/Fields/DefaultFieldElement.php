<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields;

use EzSystems\Behat\Browser\Context\BrowserContext;
use EzSystems\Behat\Browser\Element\Element;
use PHPUnit\Framework\Assert;

class DefaultFieldElement extends Element
{
    public const ELEMENT_NAME = 'DefaultFieldElement';
    private $fieldNode;

    public function __construct(BrowserContext $context, string $fieldName, string $container)
    {
        parent::__construct($context);

        $containerNode = $this->context->findElement($container);
        $this->fieldNode = $containerNode->findField($fieldName);

        Assert::assertNotNull(!$this->fieldNode, sprintf('Field %s not found.', $fieldName));
    }

    public function setValue(string $value): void
    {
        switch ($this->fieldNode->getAttribute('type')) {
            case 'text':
            case 'email':
                $this->fieldNode->setValue('');
                $this->fieldNode->setValue($value);
                break;
            case 'checkbox':
                $this->fieldNode->setValue(filter_var($value, FILTER_VALIDATE_BOOLEAN));
                break;
            case 'radio':
                if ($this->fieldNode->isChecked() !== filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
                    $this->fieldNode->click();
                }
                break;
            default:
                throw new \Exception(sprintf('Field type "%s" not defined as behat field element.', $this->fieldNode->getAttribute('type')));
        }
    }

    public function getValue(): string
    {
        switch ($this->fieldNode->getAttribute('type')) {
            case 'text':
            case 'email':
            case 'checkbox':
                return $this->fieldNode->getValue();
                break;
            case 'radio':
                return $this->fieldNode->isChecked() ? 'true' : 'false';
                break;
            default:
                throw new \Exception(sprintf('Field type "%s" not defined as behat field element.', $this->fieldNode->getAttribute('type')));
        }
    }
}
