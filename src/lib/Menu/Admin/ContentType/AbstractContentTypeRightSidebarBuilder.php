<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Menu\Admin\ContentType;

use EzSystems\EzPlatformAdminUi\Menu\AbstractBuilder;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;

abstract class AbstractContentTypeRightSidebarBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    public function createStructure(array $options): ItemInterface
    {
        /** @var \Symfony\Component\Form\FormView $contentTypeFormView */
        $contentTypeFormView = $options['form_view'];

        /** @var \Knp\Menu\ItemInterface|\Knp\Menu\ItemInterface[] $menu */
        $menu = $this->factory->createItem('root');

        $itemSaveIdentifier = $this->getItemSaveIdentifier();
        $itemCancelIdentifier = $this->getItemCancelIdentifier();

        $menu->setChildren([
            $itemSaveIdentifier => $this->createMenuItem(
                $itemSaveIdentifier,
                [
                    'attributes' => [
                        'class' => 'btn--trigger',
                        'data-click' => sprintf('#%s', $contentTypeFormView['publishContentType']->vars['id']),
                    ],
                    'extras' => ['icon' => 'save'],
                ]
            ),
            $itemCancelIdentifier => $this->createMenuItem(
                $itemCancelIdentifier,
                [
                    'attributes' => [
                        'class' => 'btn--trigger',
                        'data-click' => sprintf('#%s', $contentTypeFormView['removeDraft']->vars['id']),
                    ],
                    'extras' => ['icon' => 'circle-close'],
                ]
            ),
        ]);

        return $menu;
    }

    abstract public function getItemSaveIdentifier(): string;

    abstract public function getItemCancelIdentifier(): string;
}
