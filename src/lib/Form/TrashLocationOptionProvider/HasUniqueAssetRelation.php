<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\TrashLocationOptionProvider;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Specification\Content\ContentHaveAssetRelation;
use EzSystems\EzPlatformAdminUi\Specification\Content\ContentHaveUniqueRelation;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class HasUniqueAssetRelation implements TrashLocationOptionProvider
{
    public const TRASH_ASSETS = 'trash_assets';
    public const RADIO_SELECT_TRASH_WITH_ASSETS = 'trash_with_assets';
    public const RADIO_SELECT_DEFAULT_TRASH = 'trash_default';

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    public function __construct(
        ContentService $contentService,
        TranslatorInterface $translator
    ) {
        $this->contentService = $contentService;
        $this->translator = $translator;
    }

    public function supports(Location $location): bool
    {
        return (new ContentHaveAssetRelation($this->contentService))
            ->and((new ContentHaveUniqueRelation($this->contentService)))
            ->isSatisfiedBy($location->getContent());
    }

    public function addOptions(FormInterface $form, Location $location): void
    {
        $translatorParameters = [
            '%content_name%' => $location->getContent()->getName(),
        ];

        $form->add(self::TRASH_ASSETS, ChoiceType::class, [
            'expanded' => true,
            'multiple' => false,
            'label' =>
                /** @Desc("Asset Fields(s)") */
                $this->translator->trans('form.trash_assets.label', [], 'forms'),
            'choices' => [
                /** @Desc("Send only this Content item to Trash") */
                $this->translator->trans('location_trash_form.default_trash', $translatorParameters, 'forms') => self::RADIO_SELECT_DEFAULT_TRASH,
                /** @Desc("Send the Content item and its related assets to Trash") */
                $this->translator->trans('location_trash_form.trash_with_asset', $translatorParameters, 'forms') => self::RADIO_SELECT_TRASH_WITH_ASSETS,
            ],
            'help_multiline' => [
                /** @Desc("'%content_name%' has 1 or more asset Field(s). You have an option to send only the Content item to Trash, or to send the Content item and its assets to Trash. Please select your option:") */
                $this->translator->trans('trash_asset_single.modal.message_main', $translatorParameters),
            ],
        ]);
    }
}
