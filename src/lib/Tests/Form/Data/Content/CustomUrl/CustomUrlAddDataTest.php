<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Form\Data\Content\CustomUrl;

use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\Core\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\CustomUrl\CustomUrlAddData;
use PHPUnit\Framework\TestCase;

class CustomUrlAddDataTest extends TestCase
{
    public function testConstruct(): void
    {
        $location = new Location(['id' => 2]);
        $language = new Language(['languageCode' => 'eng-GB']);
        $path = '/test';
        $siteAccess = 'site3';

        $data = new CustomUrlAddData($location, $path, $language, false, true, $siteAccess);

        $this->assertSame($location, $data->getLocation());
        $this->assertSame($language, $data->getLanguage());
        $this->assertSame($path, $data->getPath());
        $this->assertSame($siteAccess, $data->getSiteAccess());
    }
}
