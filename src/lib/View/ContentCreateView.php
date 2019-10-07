<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\View;

use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\MVC\Symfony\View\BaseView;
use eZ\Publish\Core\MVC\Symfony\View\LocationValueView;
use Symfony\Component\Form\FormInterface;

class ContentCreateView extends BaseView implements LocationValueView, ContentTypeValueView
{
    /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType */
    private $contentType;

    /** @var \eZ\Publish\API\Repository\Values\Content\Location */
    private $location;

    /** @var \eZ\Publish\API\Repository\Values\Content\Language */
    private $language;

    /** @var \Symfony\Component\Form\FormInterface */
    private $form;

    /**
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType
     */
    public function setContentType(ContentType $contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentType
     */
    public function getContentType(): ContentType
    {
        return $this->contentType;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     */
    public function setLocation(Location $location)
    {
        $this->location = $location;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Location
     */
    public function getLocation(): Location
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
