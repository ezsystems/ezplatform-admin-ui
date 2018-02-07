<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

/** Element that describes structures in all update forms */
class UpdateForm extends Element
{
    protected $fields = [
        'formElement' => '.form-group',
        'mainFormSection' => '.px-5:nth-child(1) .card-body',
        'richTextSelector' => '.ez-data-source__richtext',
    ];

    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Admin Update Form';
    public const MAIN_FORM_SECTION = 'mainFormSection';

    public function verifyVisibility(): void
    {
        // TODO: Implement verifyVisibility() method.
    }

    public function fillFIeldWithValue(string $fieldName, string $value): void
    {
        $fieldNode = $this->context->waitUntil($this->defaultTimeout,
            function () use ($fieldName) {
                return $this->context->getSession()->getPage()->findField($fieldName);
            });

        $fieldNode->setValue('');
        $fieldNode->setValue($value);
    }

    public function fillRichtextWithValue(string $value): void
    {
        $summaryField = $this->context->findElement($this->fields['richTextSelector']);
        $summaryField->click();
        $summaryField->setValue($value);
    }
}
