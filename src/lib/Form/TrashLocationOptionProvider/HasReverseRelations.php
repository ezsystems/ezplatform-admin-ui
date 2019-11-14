<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\TrashLocationOptionProvider;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;

final class HasReverseRelations implements TrashLocationOptionProvider
{
    const TRASH_WHEN_RELATION = 'trash_with_children';

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
        $reverseRelations = $this->contentService->loadReverseRelations($location->contentInfo);

        return !empty($reverseRelations);
    }

    public function addOptions(FormInterface $form, Location $location): void
    {
        $reverseRelations = $this->contentService->loadReverseRelations($location->contentInfo);

        $translatorParameters = [
            '%content_name%' => $location->getContent()->getName(),
            '%reverse_relations%' => \count($reverseRelations),
        ];

        $form
            ->add('has_reverse_relation', ChoiceType::class, [
                'label' =>
                    /** @Desc("Conflict with reverse Relations") */
                    $this->translator->trans('form.has_reverse_relation.label', [], 'forms'),
                'help_multiline' => [
                    /** @Desc("'%content_name%' is in use by %reverse_relations% Content item(s). You should remove all reverse Relations before deleting the Content item.") */
                    $this->translator->trans('trash_container.modal.message_relations', $translatorParameters),
                ],
            ]);
    }
}
