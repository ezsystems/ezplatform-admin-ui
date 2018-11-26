<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

class FolderPreview extends PreviewPage
{
    /** @var string Name by which Page is recognised */
    public const PAGE_NAME = 'Folder Preview';

    /** @var string Name of Content Type this Preview is for */
    public const CONTENT_TYPE = 'Folder';

    public function getPageTitle(): string
    {
        return $this->context->findElement('h2')->getText();
    }

    public function verifyElements(): void
    {
    }
}
