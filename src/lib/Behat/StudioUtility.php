<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat;

use Behat\Mink\Element\NodeElement;
use Exception;
use PHPUnit\Framework\Assert;

/**
 * @deprecated These methods are used by StudioUI bundle, but should not be used in AdminUI
 */
trait StudioUtility
{
    /**
     * @deprecated use waitUntil (waitUntilVisible) that use CSS selectors
     *
     * @param $xpath
     *
     * @return mixed
     */
    public function waitForElement($xpath)
    {
        $driver = $this->getSession()->getDriver();
        $element = $this->getSession()->getPage()->waitFor(10, function () use ($driver, $xpath) {
            return $driver->find($xpath);
        });
        Assert::assertNotEmpty($element, "Element hasn't been found");

        return current($element);
    }

    /**
     * @deprecated could be extracted to a separate Element
     *
     * @Given I click on the pop-up form button :button
     * Click on a StudioUI pop-up form (e.g. "+Location")
     *
     * @param string $button Text of the element to click
     */
    public function clickPopUpForm($button)
    {
        $this->getElementByText($button, '.ezs-field__btn')->click();
    }

    /**
     * @deprecated could be extracted to a separate Element
     *
     * @Given I click on the choose content pop-up button :button
     * Click on a StudioUI choose content pop-up ("Confirm selection")
     *
     * @param string $button Text of the element to click
     */
    public function clickChooseContentPopUp($button)
    {
        $this->getElementByText($button, '.ez-button')->click();
    }

    /**
     * Clicks an element. If it was overlayed waits some time and then clicks again.
     *
     * @deprecated Should be removed when Studio rework is done (and the spinner. No need to use it in AdminUI
     *
     * @param $selector CSS Selector of the element
     * @param int $position Index of the element (in case there are more with the same selector)
     *
     * @throws UnknownError If element wasn't clicked but not because of overlay
     */
    public function clickVisibleWithPossibleOverlay(string $selector, int $position = 1)
    {
        $this->getSession()->executeScript('window.scrollTo(0,0);');

        try {
            $this->clickVisibleOfTheSameType($selector, $position);
        } catch (UnknownError $e) {
            if (strpos($e->getMessage(), 'is not clickable at point') !== false) {
                sleep(3);
                $this->clickVisibleOfTheSameType($selector, $position);

                return;
            }
            throw $e;
        }
    }

    /**
     * Finds all elements of given type by XPath literal.
     *
     * @param string $type Type of the elements to find
     * @param string $escapedValue String translated to XPath literal
     *
     * @return NodeElement[]
     */
    public function findAllOfType($type, $escapedValue): array
    {
        return $this->getSession()->getPage()->findAll('named', [$type, $escapedValue]);
    }

    /**
     * Translates string to XPath literal.
     *
     * @param string $value String to translate
     *
     * @return string
     */
    public function escapeValue($value)
    {
        return $this->getSession()->getSelectorsHandler()->xpathLiteral($value);
    }

    /**
     * @deprecated should be removed when Studio rework is done
     *
     * Wait while 'app loading' elements (such as spinner) exist
     *  for example, while opening and publishing contents, etc...
     *
     * @param $selector selector to match,
     */
    public function waitWhileLoading($selector = null, $onlyVisible = true)
    {
        if (!isset($selector)) {
            $selector = '.is-app-loading, .is-app-transitioning, .yui3-app-transitioning';
        }

        $maxTime = time() + 60;
        do {
            $elem = $this->getSession()->getPage()->find('css', $selector);
            if ($elem && $onlyVisible) {
                try {
                    $isVisible = $elem->isVisible();
                } catch (\Exception $e) {
                    // elem no longer present, assume not visible
                    $elem = null;
                }
            }
            $done = $elem == null || ($onlyVisible && !$isVisible);
        } while (!$done && time() < $maxTime);
        if (!$done) {
            throw new \Exception("Timeout while waiting for loading element '$selector'.");
        }
    }

    /**
     * Finds form fields with the same name and sets value on the visible one.
     *
     * @param string $field Name of the field
     * @param string $value Value to set
     */
    public function fillVisibleWithTheSameName($field, $value)
    {
        $elements = $this->findAllOfType('field', $field);
        foreach ($elements as $element) {
            if ($element->isVisible()) {
                $element->setValue($value);
            }
        }
    }

    /**
     * Finds HTML elements of the same type by class and clicks the n-th visible one.
     *
     * @param string $locator CSS locator of the element
     * @param int $position Index of the element to click
     *
     * @throws Exception If there is no element
     */
    public function clickVisibleOfTheSameType(string $locator, int $position = 1)
    {
        $elements = $this->findAllWithWait($locator);

        $counter = 1;
        if (!empty($elements)) {
            foreach ($elements as $element) {
                if ($element->isVisible()) {
                    if ($counter === $position) {
                        $element->click();

                        return;
                    } else {
                        ++$counter;
                    }
                }
            }
        } else {
            throw new Exception("Can't click element: Not Found");
        }
    }

    /**
     * @deprecated Use findElement instead.
     * Adpted Mink find function combined with a spin function
     * to find one element that might still be loading.
     *
     * @param   string      $selector       css selector for the element
     * @param   \Behat\Mink\Element\NodeElement $baseElement    base Mink node element from where the find should be called
     *
     * @return  \Behat\Mink\Element\NodeElement
     */
    public function getElementByClass($selector, $baseElement = null, $checkVisibility = true, $timeout = 10): NodeElement
    {
        if (!$baseElement) {
            $baseElement = $this->getSession()->getPage();
        }
        $element = $this->waitUntil($timeout,
            function () use ($selector, $baseElement, $checkVisibility) {
                $element = $baseElement->find('css', $selector);
                // An exception may be thrown if the element is not valid/attached to DOM.
                $element->getValue();

                if ($checkVisibility && !$element->isVisible()) {
                    throw new \Exception("Element with selector '$selector' is not visible");
                }

                return $element;
            }
        );

        return $element;
    }

    /**
     * @Given I select :dropdown dropdown value :value
     * @Given I select :dropdown dropdown values :value and :val2
     * @Given I select :dropdown dropdown values :value, :val2 and :val3
     *
     * @param string $dropdown Name of the dropdown
     * @param string $value Value from dropdown to select
     * @param string $val2 Second value to select
     * @param string $val3 Third value to select
     */
    public function selectDropdownValue($dropdown, $value, $val2 = null, $val3 = null)
    {
        $escapedValue = $this->escapeValue($dropdown);
        $selects = $this->findAllOfType('select', $escapedValue);
        foreach ($selects as $select) {
            if ($select->isVisible()) {
                $select->selectOption($value);
                if ($val2 != null) {
                    $select->selectOption($val2, true);
                }
                if ($val3 != null) {
                    $select->selectOption($val3, true);
                }
            }
        }
    }

    /**
     * @Given I check radio button has checked :value value
     *
     * @param string $value Value from radio button to check
     */
    public function checkRadioButtonHasChecked($value)
    {
        $this->waitWhileLoading();

        $radios = $this->findAllOfType('radio', $value);
        foreach ($radios as $radio) {
            if ($radio->isVisible()) {
                Assert::assertTrue($radio->isChecked());
            }
        }
    }

    /**
     * @Given I select Overflow radio button value :value
     * Select value from StudioUI Schedule block Overflow radio button
     *
     * @param string $value Value from radio button to select
     */
    public function selectRadioButton($value)
    {
        $this->waitWhileLoading();

        $escapedValue = $this->escapeValue($value);
        $radios = $this->findAllOfType('radio', $escapedValue);
        foreach ($radios as $radio) {
            if ($radio->isVisible()) {
                $radio->click();
            }
        }
    }
}
