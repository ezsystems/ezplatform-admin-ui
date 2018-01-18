<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat;

use Behat\Mink\Element\NodeElement;
use Behat\MinkExtension\Context\MinkContext;
use Exception;
use PHPUnit\Framework\Assert;
use WebDriver\Exception\ElementNotVisible;
use WebDriver\Exception\NoSuchElement;
use WebDriver\Exception\StaleElementReference;
use WebDriver\Exception\UnknownError;

class UtilityContext extends MinkContext
{
     /**
     * Waits until element is visible. If it does not appear throws exception.
     *
     * @param string $cssSelector
     * @param int $timeout
     *
     * @throws Exception when element not found
     */
    public function waitUntilElementIsVisible(string $cssSelector, int $timeout = 5): void
    {
        try {
            $this->waitUntil($timeout, function () use ($cssSelector) {
                $element = $this->getSession()->getPage()->find('css', $cssSelector);

                return isset($element) && $element->isVisible();
            });
        } catch (Exception $e) {
            throw new ElementNotVisible(sprintf('Element with selector: %s was not found', $cssSelector));
        }
    }

    /**
     * Waits no longer than specified timeout for the given condition to be true.
     *
     * @param int $timeoutSeconds Timeout
     * @param $callback Condition to verify
     * @param bool $throwOnFailure Whether Exception should be thrown when timeout is exceeded
     *
     * @return mixed
     *
     * @throws Exception If $throwOnFailure is true and timeout exceeded
     */
    public function waitUntil(int $timeoutSeconds, $callback, $throwOnFailure = true)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('Given callback is not a valid callable');
        }

        $start = time();
        $end = $start + $timeoutSeconds;

        do {
            try {
                $result = $callback($this);

                if ($result) {
                    return $result;
                }
            } catch (Exception $e) {
            }
            usleep(250 * 1000);
        } while (time() < $end);

        if ($throwOnFailure) {
            throw new Exception('Spin function did not return in time');
        }
    }

    // TODO: refactor
    const LOADING_SELECTOR = '.is-app-loading, .is-app-transitioning, .yui3-app-transitioning';
    const MAX_WAIT_TIMEOUT = 60;
    const WAIT_SLEEP_TIME_MS = 250;
    const SPIN_TIMEOUT = 10;

    /**
     * Adopted Mink find function to find one element that might still be loading.
     *
     * @param string $selector CSS selector for the element
     * @param int $timeout
     *
     * @return \Behat\Mink\Element\NodeElement Searched element
     */
    public function findElement(string $selector, int $timeout): ?NodeElement
    {
        return $this->waitUntil($timeout,
            function () use ($selector) {
                return $this->getSession()->getPage()->find('css', $selector);
            });
    }

    /**
     * TODO: REFACTOR BELOW TO EXISTING METHODS
     * Wait while 'app loading' elements (such as spinner) exist
     *  for example, while opening and publishing contents, etc...
     *
     * @param $selector selector to match,
     */
    public function waitWhileLoading($selector = self::LOADING_SELECTOR, $onlyVisible = true)
    {
        $maxTime = time() + self::MAX_WAIT_TIMEOUT;
        do {
            $this->sleep();
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
     * Clicks an element. If it was overlayed waits some time and then clicks again.
     *
     * @param $selector CSS Selector of the element
     * @param int $position Index of the element (in case there are more with the same selector)
     *
     * @throws UnknownError If element wasn't clicked but not because of overlay
     */
    public function clickVisibleWithPossibleOverlay($selector, $position = 1)
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
     * Finds HTML elements of the same type by class and clicks the n-th visible one.
     *
     * @param string $locator CSS locator of the element
     * @param int $position Index of the element to click
     *
     * @throws Exception If there is no element
     */
    public function clickVisibleOfTheSameType($locator, $position = 1)
    {
        $elements = $this->findAllWithWait($locator);

        $counter = 1;
        if (!empty($elements)) {
            foreach ($elements as $element) {
                if ($element->isVisible()) {
                    if ($counter == $position) {
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
     * Adpted Mink find function combined with a spin function
     * to find all element with a given css selector that might still be loading.
     *
     * @param   string      $locator        css selector for the element
     * @param   NodeElement $baseElement    base Mink node element from where the find should be called
     * @return  NodeElement[]
     */
    public function findAllWithWait($locator, $baseElement = null)
    {
        if (!$baseElement) {
            $baseElement = $this->getSession()->getPage();
        }
        $elements = $this->spin(
            function () use ($locator, $baseElement) {
                $elements = $baseElement->findAll('css', $locator);
                foreach ($elements as $element) {
                    // An exception may be thrown if the element is not valid/attached to DOM.
                    $element->getValue();
                }

                return $elements;
            }
        );

        return $elements;
    }

    /**
     * Behat spin function
     *  Execute a provided function and return it's result if valid,
     *  if an exception is thrown or the result is false wait and retry
     *  until a max timeout is reached.
     */
    public function spin($lambda)
    {
        $e = null;
        $timeLimit = time() + 10;
        do {
            try {
                $return = $lambda($this);
                if ($return) {
                    return $return;
                }
            } catch (\Exception $e) {
            }

            $this->sleep();
        } while ($timeLimit > time());

        throw new \Exception(
            'Timeout while retreaving DOM element' .
            ($e !== null ? '. Last exception: ' . $e->getMessage() : '')
        );
    }

    /**
     * Wait (sleep) for the defined time, in ms.
     */
    public function sleep()
    {
        usleep(self::WAIT_SLEEP_TIME_MS * 1000);
    }

    /**
     * Checks the visibility of a HTML element found by class. If element does not exist, false is returned.
     *
     * @param string $locator CSS locator of the element
     * @param int $timeout Number of seconds to wait until the element appears
     *
     * @return bool
     */
    public function checkVisibilityByClass($locator, $timeout = 5)
    {
        for ($i = 0; $i < $timeout; ++$i) {
            if ($this->getSession()->getPage()->find('css', $locator) == null) {
                sleep(1);
            }
        }

        $element = $this->getSession()->getPage()->find('css', $locator);

        try {
            return isset($element) && $element->isVisible();
        } catch (NoSuchElement $e) {
            return false;
        } catch (StaleElementReference $e) {
            return false;
        }
    }

    /**
     * Finds an HTML element by class and the text value and clicks it.
     *
     * @param string    $text           Text value of the element
     * @param string    $selector       CSS selector of the element
     * @param string    $textSelector   Extra CSS selector for text of the element
     * @param string    $baseElement    Element in which the search is based
     */
    public function clickElementByText($text, $selector, $textSelector = null, $baseElement = null)
    {
        $element = $this->getElementByText($text, $selector, $textSelector, $baseElement);
        if ($element && $element->isVisible()) {
            $element->click();
        } elseif ($element) {
            throw new \Exception("Can't click '$text' element: not visible");
        } else {
            throw new \Exception("Can't click '$text' element: not Found");
        }
    }

    /**
     * Finds an HTML element by class and the text value and returns it.
     *
     * @param string    $text           Text value of the element
     * @param string    $selector       CSS selector of the element
     * @param string    $textSelector   Extra CSS selector for text of the element
     * @param string    $baseElement    Element in which the search is based
     * @param int       $iteration      Iteration number, used to control number of executions
     * @return array
     */
    public function getElementByText($text, $selector, $textSelector = null, $baseElement = null)
    {
        if ($baseElement == null) {
            $baseElement = $this->getSession()->getPage();
        }
        $elements = $this->findAllWithWait($selector, $baseElement);
        foreach ($elements as $element) {
            if ($textSelector != null) {
                try {
                    $elementText = $this->findWithWait($textSelector, $element)->getText();
                } catch (\Exception $e) {
                    continue;
                }
            } else {
                $elementText = $element->getText();
            }
            if ($elementText == $text) {
                return $element;
            }
        }

        return false;
    }

    /**
     * Finds an HTML element by class and returns it.
     *
     * @param string $locator CSS locator of the element
     * @return mixed
     */
    public function getElementByClass($locator)
    {
        return $this->findWithWait($locator);
    }

    /**
     * Finds an HTML element by class and sets value.
     *
     * @param string $locator CSS locator of the element
     * @param string $value Value to fill in
     * @throws \Exception If there is no element
     */
    public function fillElementByClass($locator, $value)
    {
        $element = $this->getElementByClass($locator);
        if ($element) {
            $element->setValue($value);
        } else {
            throw new Exception("Can't set value on element: Not Found");
        }
    }

    /**
     * Adpted Mink find function combined with a spin function
     * to find one element that might still be loading.
     *
     * @param   string      $selector       css selector for the element
     * @param   \Behat\Mink\Element\NodeElement $baseElement    base Mink node element from where the find should be called
     *
     * @return  \Behat\Mink\Element\NodeElement
     */
    public function findWithWait($selector, $baseElement = null, $checkVisibility = true)
    {
        if (!$baseElement) {
            $baseElement = $this->getSession()->getPage();
        }
        $element = $this->spin(
            function () use ($selector, $baseElement, $checkVisibility) {
                $element = $baseElement->find('css', $selector);
                if (!$element) {
                    throw new \Exception("Element with selector '$selector' was not found");
                }
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
     * Finds form fields with the same name and sets value on the visible one.
     *
     * @param string $field Name of the field
     * @param string $value Value to set
     */
    public function fillVisibleWithTheSameName($field, $value)
    {
        $escapedValue = $this->escapeValue($field);
        $elements = $this->findAllOfType('field', $escapedValue);
        foreach ($elements as $element) {
            if ($element->isVisible()) {
                $element->setValue($value);
            }
        }
    }

    /**
     * Translates string to XPath literal.
     *
     * @param string $value String to translate
     * @return string
     */
    public function escapeValue($value)
    {
        return $this->getSession()->getSelectorsHandler()->xpathLiteral($value);
    }

    /**
     * Finds all elements of given type by XPath literal.
     *
     * @param string $type Type of the elements to find
     * @param string $escapedValue String translated to XPath literal
     * @return mixed
     */
    public function findAllOfType($type, $escapedValue)
    {
        return $this->getSession()->getPage()->findAll('named', array($type, $escapedValue));
    }

    /**
     * @Given I click on the pop-up form button :button
     * Click on a StudioUI pop-up form (e.g. "+Location")
     *
     * @param string $button Text of the element to click
     */
    public function clickPopUpForm($button)
    {
        $this->clickElementByText($button, '.ezs-field__btn');
    }

    /**
     * should be removed/refactored to UDW page element, but it's still the old UDW so no sens
     *
     * @When I select the :path folder in the Universal Discovery Widget
     */
    public function selectFromUniversalDiscovery($path)
    {
        // wait while UDW is hidden...
        $this->waitWhileLoading('.is-universaldiscovery-hidden');
        $node = $this->findWithWait('.ez-view-universaldiscoveryview');
        $node = $this->findWithWait('.ez-view-universaldiscoveryfinderview .ez-ud-finder-explorerlevel', $node);
        $this->openFinderExplorerPath($path, $node);
    }

    /**
     * Clicks a content browser node based on the root of the browser or a given node.
     *
     * @param   string          $text   The text of the node that is going to be clicked
     * @param   NodeElement     $parentNode       The base node to expand from, if null defaults to the content browser root
     * @throws  \Exception                  When not found
     */
    protected function openFinderExplorerNode($text, $parentNode)
    {
        $this->waitWhileLoading('.ez-ud-finder-explorerlevel-loading');

        $parentNode = $this->findWithWait('.ez-view-universaldiscoveryfinderexplorerlevelview:last-child', $parentNode);

        $element = $this->getElementByText($text, '.ez-explorer-level-list-item', '.ez-explorer-level-item', $parentNode);
        if (!$element) {
            throw new \Exception("The browser node '$text' was not found");
        }

        $element->click();
    }

    /**
     * Explores the content browser expanding it.
     *
     * @param   string       $path    The content browser path such as 'Content1/Content2/ContentIWantToClick'
     * @param   NodeElement  $node    The base node to expand from
     */
    public function openFinderExplorerPath($path, $node)
    {
        $path = explode('/', $path);
        foreach ($path as $nodeName) {
            $this->openFinderExplorerNode($nodeName, $node);
        }
    }

    public function clickChooseContentPopUp($button)
    {
        $this->clickElementByText($button, '.ez-button');
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

    public function waitForElement($xpath)
    {
        $driver = $this->getSession()->getDriver();
        $element = $this->getSession()->getPage()->waitFor(self::SPIN_TIMEOUT, function () use ($driver, $xpath) {
            return $driver->find($xpath);
        });
        Assert::assertNotEmpty($element, "Element hasn't been found");

        return current($element);
    }


    /**
     * Finds an HTML element by class and clicks it.
     *
     * @param string $locator CSS locator of the element
     * @throws \Exception If there is no element
     */
    public function clickElementByClass($locator)
    {
        $element = $this->getElementByClass($locator);
        if ($element) {
            $element->click();
        } else {
            throw new Exception("Can't click element: Not Found");
        }
    }

    /**
     * Finds an HTML element by class and does mouse over action.
     *
     * @param string $locator CSS locator of the element
     * @throws \Exception If there is no element
     */
    public function mouseOverElementByClass($locator)
    {
        $element = $this->getElementByClass($locator);
        if ($element) {
            $element->mouseOver();
        } else {
            throw new Exception("Can't mouse over element: Not Found");
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

    /**
     * Helper function which sets value on element and clears the value before.
     *
     * @param string $locator CSS locator of the element
     * @param string $value Value to fill in
     */
    public function setValueOnElementByClass($locator, $value)
    {
        $this->getElementByClass($locator)->setValue('');
        $this->getElementByClass($locator)->setValue($value);
    }

    /**
     * @Given I check radio button has checked :value value
     *
     * @param string $value Value from radio button to check
     */
    public function checkRadioButtonHasChecked($value)
    {
        $this->waitWhileLoading();

        $escapedValue = $this->escapeValue($value);
        $radios = $this->findAllOfType('radio', $escapedValue);
        foreach ($radios as $radio) {
            if ($radio->isVisible()) {
                Assert::assertTrue($radio->isChecked());
            }
        }
    }

    /**
     * @When I set :field to :value
     * @When I set :field as empty
     *
     * Spin function make it possible to retry in case of failure
     */
    public function fillFieldWithValue($field, $value = '')
    {
        $fieldNode = $this->spin(
            function () use ($field) {
                $fieldNode = $this->getSession()->getPage()->findField($field);
                if ($fieldNode == null) {
                    throw new \Exception('Field not found');
                }

                return $fieldNode;
            }
        );

        $this->spin(
            function () use ($fieldNode, $field, $value) {
                // make sure any autofocus elements don't mis-behave when setting value
                $fieldNode->blur();
                usleep(10 * 1000);
                $fieldNode->focus();
                usleep(10 * 1000);

                // setting value on pre-filled inputs can cause issues, clearing before
                $fieldNode->setValue('');
                $fieldNode->setValue($value);

                // verication that the field was really filled in correctly
                $this->sleep();
                $check = $this->getSession()->getPage()->findField($field)->getValue();
                if ($check != $value) {
                    throw new \Exception('Failed to set the field value: ' . $check);
                }

                return true;
            }
        );
    }


    /**
     * @When I fill :fieldLabel with :content
     *
     * Spin function make it possible to retry in case of failure
     */
    public function fillEditableField($fieldLabel, $content)
    {
        $this->getSession()->getPage()->findField($fieldLabel)->setValue($content);
//        $baseElement = $this->utilityContext->getElementByText($fieldLabel, '.ez-field-edit', '.ez-field-edit__label');
//        $baseElement->find('input')->setValue($content);

    }
}
