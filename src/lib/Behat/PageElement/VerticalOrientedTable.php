<?php
/**
 * Created by PhpStorm.
 * User: maciejtyrala
 * Date: 02/03/2018
 * Time: 09:00
 */

namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;


use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;

class VerticalOrientedTable extends Table
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Vertical Oriented Table';

    public function __construct(UtilityContext $context, $containerLocator)
    {
        parent::__construct($context, $containerLocator);
        $this->fields['verticalHeaders'] = $this->fields['list'].' colgroup+ tbody th';
    }
    public function getTableCellValue(string $header, ?string $secondHeader = null): string
    {
        $rowPosition = $this->context->getElementPositionByText(
            $header,
            $this->fields['verticalHeaders']
        );

        return $this->getCellValue($rowPosition, 2);
    }
}