<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\TrashLocationStrategy;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Specification\Content\ContentHaveAssetRelation;
use EzSystems\EzPlatformAdminUi\Specification\Content\ContentHaveUniqueRelation;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;

class HasAssetRelation implements TrashLocationStrategy
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \Symfony\Component\Translation\TranslatorInterface */
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
            ->and((new ContentHaveUniqueRelation($this->contentService))->not())
            ->isSatisfiedBy($location->getContent());
    }

    public function addOptions(FormInterface $form, Location $location): void
    {
        $form->add('trash_assets_non_unique', ChoiceType::class, [
            'expanded' => true,
            'multiple' => false,
            'label' =>
                /** @Desc("Asset fields(s)") */
                $this->translator->trans('form.trash_assets_non_unique.label', [], 'forms'),
            'help_multiline' => [
                /** @Desc("You are about to delete a content that has one or several asset(s) field(s) used by other content items. These assets will remain available in system.") */
                $this->translator->trans('trash_asset.modal.message_header'),
                /** @Desc("If you wish to delete this(/these) asset(s) too, first make sure they are not used by other content. You can check these content going to the asset(s) and looking at their content relations in the Relation tab.") */
                $this->translator->trans('trash_asset.modal.message_body'),
            ],
        ]);
    }
}
