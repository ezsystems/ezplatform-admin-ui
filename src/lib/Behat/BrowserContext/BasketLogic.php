<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BrowserContext;

class BasketLogic
{
    private $amount;

    public function __construct()
    {
        $this->amount = 0;
    }

    public function empty(): void
    {
        $this->amount = 0;
    }

    public function addProduct(int $productPrice): void
    {
        $this->amount += $productPrice;
    }

    public function getTotalValue(): int
    {
        return $this->amount;
    }
}
