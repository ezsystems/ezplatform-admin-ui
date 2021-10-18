<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component\Table;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\Pagination;
use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Element\Mapper\ElementTextMapper;
use Ibexa\Behat\Browser\Exception\ElementNotFoundException;
use Ibexa\Behat\Browser\Locator\CSSLocator;
use Ibexa\Behat\Browser\Locator\LocatorCollection;
use Ibexa\Behat\Browser\Locator\LocatorInterface;
use PHPUnit\Framework\Assert;

final class Table extends Component implements TableInterface
{
    private const MAX_PAGE_COUNT = 10;

    /** @var TableRowFactory */
    protected $tableFactory;

    /** @var \Ibexa\AdminUi\Behat\Component\Pagination */
    private $pagination;

    private $parentElement;

    /** @var bool */
    private $isParentElementSet;

    public function __construct(
        Session $session,
        TableRowFactory $tableFactory,
        Pagination $pagination,
        LocatorCollection $locators
    ) {
        parent::__construct($session);
        $this->pagination = $pagination;
        $this->isParentElementSet = false;
        $this->tableFactory = $tableFactory;
        $this->locators = $locators;
    }

    public function isEmpty(): bool
    {
        $this->setParentElement();

        return $this->parentElement
            ->setTimeout(0)
            ->findAll($this->locators->get('empty'))
            ->any();
    }

    public function hasElement(array $elementData): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        $hasElementOnCurrentPage = $this->hasElementOnCurrentPage($elementData);

        if ($hasElementOnCurrentPage) {
            return true;
        }

        $iterationCount = 0;

        while ($this->pagination->isNextButtonActive() && $iterationCount < self::MAX_PAGE_COUNT) {
            $this->pagination->clickNextButton();

            $hasElementOnCurrentPage = $this->hasElementOnCurrentPage($elementData);

            if ($hasElementOnCurrentPage) {
                return true;
            }

            ++$iterationCount;
        }

        return false;
    }

    public function getColumnValues(array $columnNames): array
    {
        if ($this->isEmpty()) {
            return [];
        }

        $allHeaders = $this->parentElement->findAll($this->getLocator('columnHeader'))
            ->mapBy(new ElementTextMapper());

        $foundHeaders = array_filter($allHeaders, static function (string $header) use ($columnNames) {
            return in_array($header, $columnNames, true);
        });

        $result = [];

        foreach ($foundHeaders as $headerPosition => $header) {
            $columnValues = $this->parentElement
                ->findAll($this->getTableCellLocator($headerPosition))
                ->mapBy(new ElementTextMapper());

            foreach ($columnValues as $position => $value) {
                $result[$position][$header] = $value;
            }
        }

        return $result;
    }

    public function hasElementOnCurrentPage(array $elementData): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        $searchedHeaders = array_keys($elementData);

        $allHeaders = $this->parentElement->setTimeout(10)->findAll($this->getLocator('columnHeader'))
            ->mapBy(new ElementTextMapper());

        $searchedHeadersWithPositions = $this->getHeaderPositions($searchedHeaders, $allHeaders);

        return null !== $this->getMatchingTableRow($searchedHeadersWithPositions, $elementData);
    }

    public function getTableRow(array $elementData): TableRow
    {
        if ($this->isEmpty()) {
            throw new \Exception(
                    sprintf(
                'Table row with given data was not found! Keys: %s, Values: %s',
                        implode(',', array_keys($elementData)),
                        implode(',', array_values($elementData))
                    )
            );
        }

        $searchedHeaders = array_keys($elementData);

        $allHeaders = $this->parentElement->findAll($this->getLocator('columnHeader'))
            ->mapBy(new ElementTextMapper());

        $foundHeaders = $this->getHeaderPositions($searchedHeaders, $allHeaders);
        $rowElement = $this->getMatchingTableRow($foundHeaders, $elementData);

        while (null === $rowElement && $this->pagination->isNextButtonActive()) {
            $this->pagination->clickNextButton();
            $rowElement = $this->getMatchingTableRow($foundHeaders, $elementData);
        }

        $cellLocators = [];
        foreach ($allHeaders as $headerPosition => $header) {
            $cellLocators[] = $this->getTableCellLocator($headerPosition, $header);
        }

        $filteredCellLocators = array_filter($cellLocators, static function (LocatorInterface $locator) {
            return '' !== $locator->getIdentifier();
        });

        if ($rowElement) {
            return $this->tableFactory->createRow($rowElement, new LocatorCollection($filteredCellLocators));
        }

        throw new \Exception(
            sprintf(
            'Table row with given data was not found! Keys: %s, Values: %s',
                implode(',', array_keys($elementData)),
                implode(',', array_values($elementData))
            )
        );
    }

    public function getTableRowByIndex(int $rowIndex): TableRow
    {
        if ($this->isEmpty()) {
            throw new ElementNotFoundException(sprintf('Table is empty.'));
        }

        foreach ($this->parentElement->setTimeout(0)->findAll($this->getLocator('row')) as $rowPosition => $row) {
            if ($rowPosition === $rowIndex) {
                $rowElement = $row;

                break;
            }
        }

        $allHeaders = $this->parentElement->findAll($this->getLocator('columnHeader'))
            ->mapBy(new ElementTextMapper());

        $cellLocators = [];
        foreach ($allHeaders as $headerPosition => $header) {
            $cellLocators[] = $this->getTableCellLocator($headerPosition, $header);
        }

        $filteredCellLocators = array_filter($cellLocators, static function (LocatorInterface $locator) {
            return '' !== $locator->getIdentifier();
        });

        return $this->tableFactory->createRow($rowElement, new LocatorCollection($filteredCellLocators));
    }

    public function verifyIsLoaded(): void
    {
    }

    private function setParentElement()
    {
        if ($this->isParentElementSet) {
            return;
        }

        $this->parentElement = $this->getHTMLPage()->find($this->getLocator('parent'));

        $this->isParentElementSet = true;
    }

    private function getTableCellLocator(int $headerPosition, string $identifier = 'tableCell'): CSSLocator
    {
        // +1: headerPosition is 0-indexed, but CSS selectors are 1-indexed
        return new CSSLocator($identifier, sprintf($this->getLocator('cell')->getSelector(), $headerPosition + 1, $headerPosition + 1));
    }

    protected function specifyLocators(): array
    {
        // Locators are provided through constructor
        return [];
    }

    /**
     * @param array $elementData
     */
    private function getHeaderPositions(array $searchedHeaders, array $allHeaders): array
    {
        $foundHeaders = array_filter($allHeaders, static function (string $header) use ($searchedHeaders) {
            return in_array($header, $searchedHeaders, true);
        });

        Assert::assertCount(
            count($searchedHeaders),
            $foundHeaders,
            sprintf('Could not find all expected headers in the table. Found: %s', implode(',', $foundHeaders))
        );

        return $foundHeaders;
    }

    private function getMatchingTableRow(array $foundHeaders, array $elementData): ?ElementInterface
    {
        foreach ($this->parentElement->setTimeout(3)->findAll($this->getLocator('row')) as $row) {
            foreach ($foundHeaders as $headerPosition => $header) {
                $foundHeader = $row->setTimeout(0)->findAll($this->getTableCellLocator($headerPosition));

                if ($foundHeader->empty()) {
                    // value not found, skip row
                    continue 2;
                }

                $cellValue = $foundHeader->first()->getText();
                if ($cellValue !== $elementData[$header]) {
                    // if any of the values do not match we skip the entire row
                    continue 2;
                }
            }

            // all values from the row match
            return $row;
        }

        return null;
    }
}
