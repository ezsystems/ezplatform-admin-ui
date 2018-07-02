<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\Helper\NullElement;

use Behat\Mink\Element\ElementInterface;

class NullElement implements ElementInterface
{
    /** @var string */
    private $selector;

    /** @var string */
    private $text;

    public function __construct(string $selector, string $text)
    {
        $this->selector = $selector;
        $this->text = $text;
    }

    private function raiseError()
    {
        if ($this->text !== '')
        {
            throw new NullElementException(sprintf('Attempted an action on null element with selector %s and %s', $this->selector, $this->text));
        }

        throw new NullElementException(sprintf('Attempted an action on null element with selector %s', $this->selector));
    }

    public function getXpath()
    {
        $this->raiseError();
    }

    public function getSession()
    {
        $this->raiseError();
    }

    public function has($selector, $locator)
    {
        $this->raiseError();
    }

    public function isValid()
    {
        $this->raiseError();
    }

    public function waitFor($timeout, $callback)
    {
        $this->raiseError();
    }

    public function find($selector, $locator)
    {
        $this->raiseError();
    }

    public function findAll($selector, $locator)
    {
        $this->raiseError();
    }

    public function getText()
    {
        $this->raiseError();
    }

    public function getHtml()
    {
        $this->raiseError();
    }
}
