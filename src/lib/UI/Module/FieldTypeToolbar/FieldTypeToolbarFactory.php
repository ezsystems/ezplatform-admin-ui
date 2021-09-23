<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\UI\Module\FieldTypeToolbar;

use eZ\Publish\Core\FieldType\FieldTypeRegistry;
use Ibexa\AdminUi\UI\Module\FieldTypeToolbar\Values\FieldTypeToolbar;
use Ibexa\AdminUi\UI\Module\FieldTypeToolbar\Values\FieldTypeToolbarItem;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @internal
 */
final class FieldTypeToolbarFactory
{
    /** @var \eZ\Publish\Core\FieldType\FieldTypeRegistry */
    private $fieldTypeRegistry;

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    public function __construct(FieldTypeRegistry $fieldTypeRegistry, TranslatorInterface $translator)
    {
        $this->fieldTypeRegistry = $fieldTypeRegistry;
        $this->translator = $translator;
    }

    public function create(): FieldTypeToolbar
    {
        $items = [];
        foreach ($this->getAvailableFieldTypes() as $fieldType) {
            $items[] = new FieldTypeToolbarItem(
                $fieldType->getFieldTypeIdentifier(),
                $this->getFieldTypeLabel($fieldType->getFieldTypeIdentifier()),
                $fieldType->isSingular()
            );
        }

        usort($items, static function (FieldTypeToolbarItem $a, FieldTypeToolbarItem $b): int {
            return strcmp($a->getName(), $b->getName());
        });

        return new FieldTypeToolbar($items);
    }

    /**
     * @return \eZ\Publish\API\Repository\FieldType[]
     */
    private function getAvailableFieldTypes(): iterable
    {
        foreach ($this->fieldTypeRegistry->getConcreteFieldTypesIdentifiers() as $identifier) {
            yield $this->fieldTypeRegistry->getFieldType($identifier);
        }
    }

    /**
     * Generate a human-readable name for field type identifier.
     */
    private function getFieldTypeLabel(string $fieldTypeIdentifier): string
    {
        return $this->translator->trans(/** @Ignore */ $fieldTypeIdentifier . '.name', [], 'fieldtypes');
    }
}
