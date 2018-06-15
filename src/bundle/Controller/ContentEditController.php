<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use EzSystems\EzPlatformAdminUi\View\ContentTranslateSuccessView;
use EzSystems\EzPlatformAdminUi\View\ContentTranslateView;

class ContentEditController extends Controller
{
    /**
     * @param \EzSystems\EzPlatformAdminUi\View\ContentTranslateView $view
     *
     * @return \EzSystems\EzPlatformAdminUi\View\ContentTranslateView
     */
    public function translateAction(ContentTranslateView $view): ContentTranslateView
    {
        return $view;
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\View\ContentTranslateSuccessView $view
     *
     * @return \EzSystems\EzPlatformAdminUi\View\ContentTranslateSuccessView
     */
    public function translationSuccessAction(ContentTranslateSuccessView $view): ContentTranslateSuccessView
    {
        return $view;
    }
}
