<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\View;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\Symfony\View\BaseView;
use eZ\Publish\Core\MVC\Symfony\View\ContentValueView;
use eZ\Publish\Core\MVC\Symfony\View\LocationValueView;
use Symfony\Component\Form\FormInterface;

class ContentEditView extends BaseView implements ContentValueView, LocationValueView
{
    /** @var \eZ\Publish\API\Repository\Values\Content\Content */
    private $content;

    /** @var \eZ\Publish\API\Repository\Values\Content\Location|null */
    private $location;

    /** @var \eZ\Publish\API\Repository\Values\Content\Language */
    private $language;

    /** @var \Symfony\Component\Form\FormInterface */
    private $form;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     */
    public function setContent(Content $content)
    {
        $this->content = $content;
    }

    /**
     * Returns the Content.
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     */
    public function getContent(): Content
    {
        return $this->content;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location|null $location
     */
    public function setLocation(?Location $location)
    {
        $this->location = $location;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Location|null
     */
    public function getLocation(): ?Location
    {
        return $this->location;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Language
     */
    public function getLanguage(): Language
    {
        return $this->language;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Language $language
     */
    public function setLanguage(Language $language)
    {
        $this->language = $language;
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm(): FormInterface
    {
        return $this->form;
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     */
    public function setForm(FormInterface $form)
    {
        $this->form = $form;
    }
}
