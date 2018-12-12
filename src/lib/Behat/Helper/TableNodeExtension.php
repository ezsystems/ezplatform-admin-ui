<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\Helper;

use Behat\Gherkin\Node\TableNode;

class TableNodeExtension extends TableNode
{
    /**
     * Adds a column (in form: ['header' => [values]] or ['header' => 'value']) to a given table.
     *
     * @param TableNode $table
     * @param array $columnData
     *
     * @return TableNode
     *
     * @throws \Behat\Gherkin\Exception\NodeException
     */
    public static function addColumn(TableNode $table, array $columnData): TableNode
    {
        $headers = array_keys($columnData);

        $newParameters = $table->getTable();

        foreach ($headers as $header) {
            $row = array_keys($table->getTable())[0];
            $newParameters[$row++][] = $header;
            if (\is_array($columnData[$header])) {
                foreach ($columnData[$header] as $value) {
                    $newParameters[$row++][] = $value;
                }
            } else {
                $newParameters[$row][] = $columnData[$header];
            }
        }

        return new self($newParameters);
    }
}
