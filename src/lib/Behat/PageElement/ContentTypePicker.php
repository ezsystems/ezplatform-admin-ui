<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\Behat\Browser\Context\BrowserContext;
use EzSystems\Behat\Browser\Element\Element;

class ContentTypePicker extends Element
{
    public const ELEMENT_NAME = 'ContentTypePicker';
    public $fields;

    public function __construct(BrowserContext $context)
    {
        parent::__construct($context);
        $this->fields = [
            'contentTypeSelector' => '.form-check-label',
        ];
    }

    public function select(string $contentType): void
    {
        $this->context->getElementByText($contentType, $this->fields['contentTypeSelector'])->click();
    }
}
