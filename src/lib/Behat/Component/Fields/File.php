<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component\Fields;

use Behat\Mink\Session;
use Ibexa\Behat\Browser\FileUpload\FileUploadHelper;
use Ibexa\Behat\Browser\Locator\CSSLocator;
use Ibexa\Behat\Browser\Locator\CSSLocatorBuilder;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class File extends FieldTypeComponent
{
    /** @var \Ibexa\Behat\Browser\FileUpload\FileUploadHelper */
    private $fileUploadHelper;

    public function __construct(Session $session, FileUploadHelper $fileUploadHelper)
    {
        parent::__construct($session);
        $this->fileUploadHelper = $fileUploadHelper;
    }

    public function setValue(array $parameters): void
    {
        $fieldSelector = CSSLocatorBuilder::base($this->getLocator('fieldInput'))
            ->withParent($this->parentLocator)
            ->build();

        $this->getHTMLPage()->find($fieldSelector)->attachFile(
            $this->fileUploadHelper->getRemoteFileUploadPath($parameters['value'])
        );
    }

    public function verifyValueInItemView(array $values): void
    {
        $filename = str_replace('.zip', '', $values['value']);

        Assert::assertStringContainsString(
            $filename,
            $this->getHTMLPage()->find($this->parentLocator)->getText(),
            'Image has wrong file name'
        );

        $fileFieldSelector = CSSLocatorBuilder::base($this->parentLocator)
            ->withDescendant($this->getLocator('file'))
            ->build();

        Assert::assertStringContainsString(
            $filename,
            $this->getHTMLPage()->find($fileFieldSelector)->getAttribute('href'),
            'File has wrong href'
        );
    }

    public function specifyLocators(): array
    {
        return [
            new CSSLocator('fieldInput', 'input[type=file]'),
            new VisibleCSSLocator('file', '.ezbinaryfile-field a'),
        ];
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ezbinaryfile';
    }
}
