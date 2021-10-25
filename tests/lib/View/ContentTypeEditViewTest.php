<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\AdminUi\View;

use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeDraft;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup;
use Ibexa\AdminUi\View\ContentTypeEditView;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

final class ContentTypeEditViewTest extends TestCase
{
    public function testGetParameters(): void
    {
        $formView = $this->createMock(FormView::class);

        $form = $this->createMock(FormInterface::class);
        $form->method('createView')->willReturn($formView);

        $contentTypeDraft = $this->createMock(ContentTypeDraft::class);
        $contentTypeGroup = $this->createMock(ContentTypeGroup::class);

        $language = $this->createMock(Language::class);
        $language->method('__get')->with('languageCode')->willReturn('eng-GB');

        $view = new ContentTypeEditView(
            'edit.html.twig',
            $contentTypeGroup,
            $contentTypeDraft,
            $language,
            $form
        );

        self::assertEquals(
            [
                'content_type_group' => $contentTypeGroup,
                'content_type' => $contentTypeDraft,
                'form' => $formView,
                'language_code' => 'eng-GB',
            ],
            $view->getParameters()
        );
    }
}
