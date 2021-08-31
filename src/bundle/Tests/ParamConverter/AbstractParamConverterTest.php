<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Tests\ParamConverter;

use eZ\Bundle\EzPublishCoreBundle\Tests\Converter\AbstractParamConverterTest as CoreAbstractParamConverterTest;

abstract class AbstractParamConverterTest extends CoreAbstractParamConverterTest
{
    const SUPPORTED_CLASS = null;

    public function testSupports()
    {
        $config = $this->createConfiguration(static::SUPPORTED_CLASS);

        $this->assertTrue($this->converter->supports($config));
    }
}
