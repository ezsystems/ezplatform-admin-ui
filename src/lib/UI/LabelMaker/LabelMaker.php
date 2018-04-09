<?php
namespace EzSystems\EzPlatformAdminUi\UI\LabelMaker;

/**
 * Makes human readable labels for UI items given an item and a label identifier.
 */
interface LabelMaker
{
    /**
     * @param string $identifier Identifier of the label to make. Example: 'description'.
     * @param string mixed $items Components of the generated item. Example: a custom tag's identifier & attribute.
     * @param bool $generateDefault Wether or not to generate a default label if none is set.
     * @return string The label if one exists.
     *                If none exists, a default is built based on $defaultBase, unless it is set to false.
     *
     */
    public function getLabel(string $identifier, $items, bool $generateDefault = true): string;
}