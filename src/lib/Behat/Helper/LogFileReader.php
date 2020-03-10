<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\Helper;

class LogFileReader
{
    public function getLastLines($filePath, $numberOfLines): array
    {
        $logEntries = [];
        $counter = 0;

        $file = @fopen($filePath, 'r');

        if ($file === false) {
            return [];
        }

        while (!feof($file)) {
            if ($counter >= $numberOfLines) {
                array_shift($logEntries);
            }

            $line = fgets($file);
            $logEntries[] = str_replace(PHP_EOL, '', $line);
            ++$counter;
        }

        fclose($file);

        return $logEntries;
    }
}
